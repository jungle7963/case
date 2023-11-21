<template>
  <el-drawer
    ref="drawer"
    :with-header="false"
    size="50%"
    :before-close="handleClose"
    :visible.sync="dialogFormVisible"
    direction="rtl"
    custom-class="demo-drawer"
  >
    <div class="demo-drawer__content">
      <el-form ref="dataForm" :rules="rules" :model="temp" v-loading="listLoading" label-position="left" label-width="120px" style="width: 100%; padding:10px; height: 100vh;overflow-y: scroll;">
        <el-tabs tab-position="top" style="height: 200px;">
          <el-tab-pane label="基本信息">
            <el-progress id="progress" type="circle" :percentage="temp.percentage" :stroke-width="9" v-show="pgstatus"></el-progress>
            <div id="speed" v-show="pgstatus">{{speed}}</div>
            <el-form-item label="分步下载">
              <el-button type="success" round size="medium" @click="mp4Download" :disabled="disabled1">视频下载</el-button>
              <el-button type="success" round size="medium" @click="mp3Transcription" :disabled="disabled2">视频转音频</el-button>
              <el-button type="success" round size="medium" @click="ossUpload" :disabled="disabled3">上传OSS</el-button>
              <el-button type="success" round size="medium" @click="mp3Translate" :disabled="disabled4">语音识别</el-button>
            </el-form-item>

            <el-form-item label="wordpress接口" prop="information_id">
              <el-select v-model="temp.information_id" class="filter-item" placeholder="请选择" clearable>
                <el-option @click.native="getCategory" v-for="item in informations" :key="item.id" :label="item.interface_name" :value="item.id" />
              </el-select>
            </el-form-item>

            <el-form-item label="类别" prop="category">
              <el-select v-model="temp.category" class="filter-item" placeholder="请选择">
                <el-option v-for="item in categories" :key="item.id" :label="item.category" :value="item.id" />
              </el-select>
            </el-form-item>

            <el-form-item label="别名" prop="alias" >
              <el-input placeholder="别名" v-model="temp.alias" />
            </el-form-item>

            <el-form-item label="文章链接" prop="article_link" >
              <el-input v-model="temp.article_link" disabled/>
            </el-form-item>

            <el-form-item label="标题" prop="title" >
              <el-input placeholder="请输入发布标题" v-model="temp.title" />
            </el-form-item>

            <el-form-item label="视频链接" prop="link" >
              <el-input placeholder="请输入视频链接" v-model="temp.link" />
              <el-button  size="mini" type="success" @click="setvlink()">添加视频</el-button>
            </el-form-item>

            <el-form-item label="语音识别">
              <el-tabs type="border-card">
                <el-tab-pane label="富文本模式" prop="content" >
                  <quillEditor
                          v-model="temp.content"
                          ref="myQuillEditor"
                          :options="editorOption"
                  >
                  </quillEditor>
                </el-tab-pane>
                <el-tab-pane label="歌词模式" prop="lyric">
                  <el-input v-model="temp.lyric" rows="29" type="textarea" clearable />
                </el-tab-pane>
                <el-tab-pane label="原始模式" prop="original">
                  <el-input v-model="temp.original" rows="29" type="textarea" clearable />
                </el-tab-pane>
              </el-tabs>
            </el-form-item>
          </el-tab-pane>
        </el-tabs>
      </el-form>
      <div class="demo-drawer__footer" style="position:fixed;top:15px;right:30px;">
        <el-button size="mini" @click="$refs.drawer.closeDrawer()">取 消</el-button>
        <el-button size="mini" :loading="btnLoading" type="primary" @click="saveData()">保存</el-button>
        <el-button size="mini" :loading="releaseing" type="success" @click="release()">发布</el-button>
      </div>
    </div>

  </el-drawer>

</template>

<script>
import { getCategory, getListAll as getListAllinformation } from '@/api/information'
import {mp4Download, mp3Transcription, ossUpload, mp3Translate, getinfo, save, release, change, getprogress} from '@/api/youtube'
import { quillEditor } from "vue-quill-editor"; //调用编辑器
import 'quill/dist/quill.core.css';
import 'quill/dist/quill.snow.css';
import 'quill/dist/quill.bubble.css';

export default {
  name: 'YoutubeForm',
  components: { quillEditor },
  data() {
    return {
      speed:'',
      pgstatus: false,
      disabled1: false,
      disabled2: false,
      disabled3: false,
      disabled4: false,
      btnLoading: false,
      releaseing: false,
      listLoading: false,
      informations:null,
      categories:null,
      current_category:null,
      oldtemp:{
        information_id:'',
        title:'',
        content: '',
        original:'',
        lyric:'',
      },
      temp: {
        id: 0,
        information_id:'',
        link:'',
        title:'',
        classify:'',
        content: '',
        original:'',
        lyric:'',
        status:0,
        percentage:0,
        article_link:'',
        category:'',
        alias:'',
        article_url:'',
      },
      dialogFormVisible: false,
      editorOption: {},
      rules: {}
    }
  },
  computed: {
  },
  watch: {
    dialogFormVisible: function() {
      this.resetTemp()
    },

    pgstatus: function() {
      if (this.pgstatus === true){
        this.getPercentage();
      }
    },

    'temp.category'() {
      if (this.temp.information_id != null && this.temp.information_id != '' && this.temp.category != 'uncategorised'){
        let alias = this.temp.alias
        if (alias == ''){
          alias = this.temp.title
        }
        let category = this.categories.filter((num) => {
          if (num.id == this.temp.category){
            return num.category
          }
        });
        this.current_category = category[0]['category']
        this.temp.article_link = this.temp.article_url + '/' + this.current_category + '/' + alias
      }
    },

    'temp.alias'() {
      if (this.temp.information_id != null && this.temp.information_id != ''){
        let alias = this.temp.alias
        if (alias == ''){
          alias = this.temp.title
        }
        this.temp.article_link = this.temp.article_url + '/' + this.current_category + '/' + alias
      }
    },

    'temp.title'() {
      if (this.temp.information_id != null && this.temp.information_id != '' && this.temp.alias == ''){
        let alias = this.temp.title
        this.temp.article_link = this.temp.article_url + '/' + this.current_category + '/' + alias
      }
    },

    temp: {
      handler(newVal, oldVal) {},
      immediate: true,
      deep: true
    }
  },

  created() {
    this.getInformation()
  },

  methods: {
    handleClose() {
        if (
                this.temp.title == this.oldtemp.title &&
                this.temp.content == this.oldtemp.content &&
                this.temp.original == this.oldtemp.original &&
                this.temp.lyric == this.oldtemp.lyric)
        {
          this.dialogFormVisible = false
        }else{
          if (this.btnLoading) {
            return
          }
          this.$confirm('更改将不会被保存，确定要取消吗？')
                  .then(() => {
                    this.dialogFormVisible = false
                  })
                  .catch(() => {})
        }
    },

    getCategory() {
      this.listLoading = true
      this.temp.category = 'uncategorised'
      this.current_category = 'uncategorised'
      this.temp.alias = this.temp.title
      this.temp.article_url = ''
      getCategory(this.temp).then(response => {
          if (response.status === 1){
              this.categories = response.data.data;
              this.temp.article_url = response.data.url
              this.temp.article_link = this.temp.article_url + '/' + this.temp.category + '/' + this.temp.alias
          }
      }).finally(() => {
        this.listLoading = false
      })
    },

    setvlink(){
      let link = this.temp.link.replace("https://www.youtube.com/watch?v=","https://www.youtube.com/embed/")
      this.$refs.myQuillEditor.quill.clipboard.dangerouslyPasteHTML(
              this.temp.content+`<iframe src="${link}" frameborder="0" allowfullscreen></iframe>`
      )
    },

    getInformation() {
      getListAllinformation().then(response => {
        let arr = response.data.data
        let filterArr = [1];
        this.informations = arr.filter((val) => {
          return filterArr.includes(val.status)
        })
      })
    },

    resetTemp() {
      this.temp = {
        id: 0,
        information_id:'',
        link:'',
        classify:'',
        title:'',
        content: '',
        original:'',
        lyric:'',
        status:0,
        percentage:0,
        article_link:'',
        category:'',
        alias:'',
        article_url:'',
      }
    },

    handleUpdate(id) {
      this.dialogFormVisible = true
      this.btnLoading = false
      this.releaseing = false
      const _this = this
      getinfo(id).then(response => {
        if (response.status === 1) {
          _this.temp.id = response.data.id
          _this.temp.link = response.data.link
          _this.temp.title = response.data.title
          _this.temp.content = response.data.content
          _this.temp.original = response.data.original
          _this.temp.lyric = response.data.lyric
          _this.temp.classify = response.data.classify

          _this.temp.alias = response.data.title

          _this.oldtemp.title = response.data.title
          _this.oldtemp.content = response.data.content
          _this.oldtemp.original = response.data.original
          _this.oldtemp.lyric = response.data.lyric
        }
      })
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate()
      })
    },

    saveData() {
      this.btnLoading = true
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const _this = this
          save(this.temp).then(response => {
            if (response.status === 1) {
              if (!_this.temp.id) {
                _this.temp.id = response.data.id
              }
              this.$emit('updateRow', _this.temp)
              _this.dialogFormVisible = false
              _this.$message.success(response.msg)
            } else {
              _this.$message.error(response.msg)
            }
            _this.btnLoading = false
          })
        } else {
          this.btnLoading = false
        }
      })
    },

    getPercentage(){
      const _this = this;
      var a = setInterval(function () {
        getprogress().then(response => {
          _this.temp.percentage = JSON.parse(response.progress)
          _this.speed = response.speed
          if (_this.temp.percentage === 100  || _this.pgstatus === false) {
            setTimeout(() => {
              _this.pgstatus = false
              if (_this.temp.classify == 'bilibili'){
                _this.$notify.success('视频正在合成解析，请等待！')
              }
            },1000)
            clearInterval(a)
            return
          }
        })
      }, 1000);
    },

    mp4Download() {
      this.speed = '';
      this.temp.percentage = 0;
      this.pgstatus = true;
      this.disabled1 = true;
      this.$notify.success('正在下载视频')
      const _this = this;
      mp4Download(this.temp).then(response => {
        if (response.status === 1) {
          _this.$notify.success(response.msg)
        }else if (response.status === undefined){
          _this.$notify.error('下载出错！')
        }else {
          _this.$notify.error(response.msg)
        }
        if (this.temp.classify == 'kuaishou' || this.temp.classify == 'douyin') {
          this.pgstatus = false
        }
        this.disabled1 = false;
        this.handleUpdate(_this.temp.id);
      }).catch(() => {
        this.pgstatus = false;
        this.disabled1 = false;
      })
    },

    mp3Transcription() {
      this.disabled2 = true;
      this.$notify.success('正在视频转音频')
      const _this = this;
      mp3Transcription(this.temp).then(response => {
        if (response.status === 1) {
          _this.$notify.success(response.msg)
        } else {
          _this.$notify.error(response.msg)
        }
        this.disabled2 = false;
      }).catch(() => {
        this.disabled2 = false;
      })
    },

    ossUpload() {
      this.disabled3 = true;
      this.$notify.success('正在上传OSS')
      const _this = this;
      ossUpload(this.temp).then(response => {
        if (response.status === 1) {
          _this.$notify.success(response.msg)
        } else {
          _this.$notify.error(response.msg)
        }
        this.disabled3 = false;
      }).catch(() => {
        this.disabled3 = false;
      })
    },

    mp3Translate() {
      this.disabled4 = true;
      this.$notify({
        message: '正在语音识别！',
        type: 'success',
        duration: 8000
      })
      const _this = this;
      mp3Translate(this.temp).then(response => {
        if (response.status === 1) {
          _this.$notify.success(response.msg)
        } else {
          _this.$notify.error(response.msg)
        }
        this.disabled4 = false;
        this.handleUpdate(_this.temp.id);
      }).catch(() => {
        this.disabled4 = false;
      })
    },

    release(){
      this.releaseing = true
      const _this = this
      release(this.temp).then(response => {
        if (response.code == 10015){
          this.$message({
            type: 'success',
            message: '已发布到wordpress'
          })
          change(_this.temp.id, 'status', 1).then(response => {})
          _this.temp.status = 1;
          this.$emit('updateRow', _this.temp)
          _this.dialogFormVisible = false
        }else {
          this.$message({
            type: 'error',
            message: response.msg
          })
        }
      }).finally(() => {
        _this.releaseing = false
      })
    }
  }
}
</script>

<style lang="scss" scoped>
  /deep/ .ql-editor{
    min-height: 500px;
  }

  #progress{
    position:fixed;
    top: 20px;
    left: 450px;
    z-index: 1999999999;
  }

  #speed{
    position:fixed;
    top: 100px;
    left: 485px;
    z-index: 1999999999;
    font-size: 10px;
    text-align: center;
  }
  /*/deep/ .el-progress-circle__track {*/
  /*  stroke: #13ce66;*/
  /*}*/

  /deep/ .el-progress__text {
    color: #13ce66;
    font-weight: 600;
  }
</style>

