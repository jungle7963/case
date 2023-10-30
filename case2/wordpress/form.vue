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
    <div class="demo-drawer__content" v-if="showCreate">
      <el-form ref="dataForm" :rules="rules" :model="temp" v-loading="listLoading" label-position="left" label-width="130px" style="width: 100%; padding:10px; height: 100vh;overflow-y: scroll;">
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

            <el-form-item label="文本" prop="description">
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
        <el-button size="mini" :loading="btnLoading" type="success" @click="release()">发布</el-button>
      </div>
    </div>

    <div class="demo-drawer__content" v-if="showUpdate">
          <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="130px" style="width: 100%; padding:10px; height: 100vh;overflow-y: scroll;">
              <el-tabs tab-position="top" style="height: 200px;">
                  <el-tab-pane label="基本信息">
                      <el-form-item label="文章链接">
                          <el-input v-model="temp.wordpress_url" disabled/>
                      </el-form-item>

                      <el-form-item label="视频标题">
                          <el-input v-model="temp.title" disabled/>
                      </el-form-item>

                      <el-form-item label="视频链接">
                          <el-input v-model="temp.link" disabled/>
                      </el-form-item>

                      <el-form-item label="文本">
                          <el-tabs type="border-card">
                              <quillEditor
                                      v-model="temp.description"
                                      ref="myQuillEditor"
                                      :options="editorOption"
                                      disabled
                              >
                              </quillEditor>
                          </el-tabs>
                      </el-form-item>
                  </el-tab-pane>
              </el-tabs>
          </el-form>
          <div class="demo-drawer__footer" style="position:fixed;top:15px;right:30px;">
              <el-button size="mini" @click="$refs.drawer.closeDrawer()">关 闭</el-button>
          </div>
      </div>

  </el-drawer>
</template>

<script>
  import { getCategory, getListAll as getListAllinformation } from '@/api/information'
  import { getinfo, release } from '@/api/wordpress'
  import { quillEditor } from "vue-quill-editor"; //调用编辑器
  import 'quill/dist/quill.core.css';
  import 'quill/dist/quill.snow.css';
  import 'quill/dist/quill.bubble.css';
  export default {
    name: 'WordpressForm',
    components: { quillEditor },
    data() {
      return {
        editorOption: {},
        informations:null,
        btnLoading: false,
        showCreate: false,
        showUpdate: false,
        listLoading: false,
        columns: null,
        categories:null,
        current_category:null,
        temp: {
          id: 0,
          information_id:'',
          link:'',
          url:'',
          title:'',
          description:'',
          article_link:'',
          category:'',
          alias:'',
          article_url:'',
        },
        oldtemp:{
          information_id:'',
          title:'',
          description:'',
        },
        dialogFormVisible: false,
        rules: {
          information_id: [{ required: true, message: 'wordpress接口必填', trigger: 'blur' }],
          title: [{ required: true, message: '标题必填', trigger: 'blur' }],
          description: [{ required: true, message: '文本必填', trigger: 'blur' }]
        }

      }
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

      handleClose(done) {
        this.showUpdate = false
        this.showCreate = false
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
          url:'',
          title:'',
          description:'',
            article_link:'',
            category:'',
            alias:'',
            article_url:'',
        }
      },
      release(){
        this.btnLoading = true
        this.$refs['dataForm'].validate((valid) => {
          if (valid) {
            const _this = this
            release(this.temp).then(response => {
              if (response.code == 10015){
                this.$message({
                  type: 'success',
                  message: '已发布到wordpress'
                })
                this.$emit('updateRow')
                _this.dialogFormVisible = false
              }else {
                this.$message({
                  type: 'error',
                  message: response.msg
                })
              }
            }).finally(() => {
                _this.btnLoading = false
            })
          } else {
            this.btnLoading = false
          }
        })
      },
      handleCreate() {
        this.dialogFormVisible = true
        this.showCreate = true
        this.btnLoading = false
        this.currentIndex = -1
        this.oldtemp.information_id = ''
        this.oldtemp.title = ''
        this.oldtemp.description = ''
        this.$nextTick(() => {
          this.$refs['dataForm'].clearValidate()
        })
      },
      handleUpdate(id) {
        this.dialogFormVisible = true
        this.showUpdate = true
        this.btnLoading = false
        const _this = this
        getinfo(id).then(response => {
          if (response.status === 1) {
            _this.temp.id = response.data.id
            _this.temp.wordpress_url = response.data.wordpress_url
            _this.temp.title = response.data.title
            _this.temp.url = response.data.url
            _this.temp.link = response.data.link
            _this.temp.description = response.data.description

            _this.oldtemp.title = response.data.title
            _this.oldtemp.description = response.data.description
          }
        })
        this.$nextTick(() => {
          this.$refs['dataForm'].clearValidate()
        })
      },
    }
  }
</script>

<style lang="scss" scoped>
  /deep/ .ql-editor{
    min-height: 530px;
  }
</style>

