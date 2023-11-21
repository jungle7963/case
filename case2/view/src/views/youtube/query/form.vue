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
import {captions, saveQuery, publish} from '@/api/youtube'
import { quillEditor } from "vue-quill-editor"; //调用编辑器
import 'quill/dist/quill.core.css';
import 'quill/dist/quill.snow.css';
import 'quill/dist/quill.bubble.css';

export default {
  name: 'QueryForm',
  components: { quillEditor },
  data() {
    return {
      list: null,
      listLoading: false,
      btnLoading: false,
      releaseing: false,
      informations:null,
      categories:null,
      current_category:null,
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
        langs:'',
        url:'',
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
      this.dialogFormVisible = false
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
        description:'',
        langs:'',
        url:'',
        article_link:'',
        category:'',
        alias:'',
        article_url:'',
      }
    },

    handleUpdate(v) {
      this.listLoading = true
      this.dialogFormVisible = true
      this.btnLoading = false
      this.releaseing = false
      this.list = v
      captions(v.link).then(response => {
        this.temp.description = response[0]
        this.temp.langs = response[1]
        this.temp.url = response[2]
      }).catch(() => {
        this.$notify.warning('抱歉！该视频没有字幕')
      }).finally(() => {
        this.listLoading = false
        this.temp.title = v.title
        this.temp.alias = v.title
        this.temp.link = v.link
      })
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate()
      })
    },

    saveData() {
      this.btnLoading = true
      saveQuery(this.list).then(response => {
        if (response.status === 1){
          this.$message.success(response.msg)
          this.dialogFormVisible = false
        }else {
          this.$message.error(response.msg)
        }
      }).finally(() => {
        this.btnLoading = false
      })
    },

    release(){
      this.releaseing = true
      let list = { ...this.list, information_id : this.temp.information_id}
      list = { ...list, langs : this.temp.langs}
      list = { ...list, description : this.temp.description}
      list = { ...list, url : this.temp.url}
      list = { ...list, alias : this.temp.alias}
      list.title = this.temp.title
      const _this = this
      publish(list).then(response => {
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
      }).finally(() => {
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

