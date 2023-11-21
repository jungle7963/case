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

            <el-form-item label="语言" prop="lang">
              <el-select v-model="temp.lang" class="filter-item" placeholder="请选择" clearable>
                <el-option v-for="item in langs" :key="item" :label="item" :value="item" />
              </el-select>
              <el-button  size="medium" type="success" @click="caption()">查询字幕</el-button>
            </el-form-item>
            <el-form-item label="文本">
              <el-tabs type="border-card">
                  <quillEditor
                          v-model="temp.description"
                          ref="myQuillEditor"
                          :options="editorOption"
                  >
                  </quillEditor>
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
import {getinfo, save, release} from '@/api/ytvideo'
import {caption} from '@/api/youtube'
import { quillEditor } from "vue-quill-editor"; //调用编辑器
import 'quill/dist/quill.core.css';
import 'quill/dist/quill.snow.css';
import 'quill/dist/quill.bubble.css';

export default {
  name: 'YtvideoForm',
  components: { quillEditor },
  data() {
    return {
      btnLoading: false,
      releaseing: false,
      listLoading: false,
      informations:null,
      categories:null,
      current_category:null,
      langs:null,
      oldtemp:{
        information_id:'',
        title:'',
        description: '',
      },
      temp: {
        id: 0,
        information_id:'',
        link:'',
        title:'',
        description:'',
        lang:'',
        langs:'',
        url:'',
        article_link:'',
        category:'',
        alias:'',
        article_url: ''
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
                this.temp.description == this.oldtemp.description)
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

    caption(){
      this.listLoading = true
      caption(this.temp).then(response => {
        this.temp.description = response
        this.listLoading = false
      })
    },

    setvlink(){
      this.$refs.myQuillEditor.quill.clipboard.dangerouslyPasteHTML(
              this.temp.description+`<iframe src="${this.temp.link}" frameborder="0" allowfullscreen></iframe>`
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
        title:'',
        lang:'',
        langs:'',
        url:'',
        description:'',
        article_link:'',
        category:'',
        alias:'',
        article_url:'',
      }
    },

    handleUpdate(id) {
      this.listLoading = true
      this.dialogFormVisible = true
      this.btnLoading = false
      this.releaseing = false
      const _this = this
      getinfo(id).then(response => {
        if (response.status === 1) {
          _this.temp.id = response.data.id
          _this.temp.link = response.data.link
          _this.temp.title = response.data.title
          _this.temp.description = response.data.description
          _this.temp.lang = response.data.lang
          _this.temp.url = response.data.url
          _this.langs = response.data.langs.substring(2,response.data.langs.length-2).split('","')

          _this.temp.alias = response.data.title

          _this.oldtemp.title = response.data.title
          _this.oldtemp.description = response.data.description
        }
      }).finally(() => {
        _this.listLoading = false
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
              _this.dialogFormVisible = false
              _this.$message.success(response.msg)
            } else {
              _this.$message.error(response.msg)
            }
          }).finally(() => {
            _this.btnLoading = false
          })
        } else {
          this.btnLoading = false
        }
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
          _this.dialogFormVisible = false
        }else {
          this.$message({
            type: 'error',
            message: response.msg
          })
        }
      }).finally(() =>{
        _this.releaseing = false
      })
    }
  }
}
</script>

<style lang="scss" scoped>
  /deep/ .ql-editor{
    min-height: 530px;
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

