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
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="130px" style="width: 100%; padding:10px; height: 100vh;overflow-y: scroll;">

        <el-tabs tab-position="top" style="height: 200px;">
          <el-tab-pane label="基本信息">
            <el-form-item label="用户名" prop="username">
              <el-input v-model="temp.username" clearable />
            </el-form-item>
            <el-form-item label="密码" prop="password">
              <el-input v-model="temp.password" clearable />
            </el-form-item>
            <el-form-item label="接口名" prop="interface_name">
              <el-input v-model="temp.interface_name" clearable />
            </el-form-item>
            <el-form-item label="wordpress接口" prop="url">
              <el-input v-model="temp.url" clearable />
            </el-form-item>
            <el-form-item label="状态">
              <el-radio-group v-model="temp.status">
                <el-radio :label="1">正常</el-radio>
                <el-radio :label="0">禁用</el-radio>
              </el-radio-group>
            </el-form-item>

          </el-tab-pane>
        </el-tabs>

      </el-form>
      <div class="demo-drawer__footer" style="position:fixed;top:15px;right:30px;">
        <el-button size="mini" @click="$refs.drawer.closeDrawer()">取 消</el-button>
        <el-button size="mini" :loading="btnLoading" type="primary" @click="saveData()">保存</el-button>
      </div>
    </div>
  </el-drawer>
</template>

<script>
  import { getinfo, save } from '@/api/information'
  export default {
    name: 'InformationForm',
    components: { },
    data() {
      return {
        btnLoading: false,
        columns: null,
        temp: {
          id: 0,
          username: '',
          password: '',
          interface_name: '',
          url: '',
          status: 1,
        },
        oldtemp:{
          username:'',
          password:'',
          interface_name:'',
          url: '',
          status:'',
        },
        dialogFormVisible: false,
        rules: {
          username: [{ required: true, message: '用户名必填', trigger: 'blur' }],
          password: [{ required: true, message: '密码必填', trigger: 'blur' }],
          interface_name: [{ required: true, message: '接口名必填', trigger: 'blur' }],
          url: [{ required: true, message: 'wordpress接口必填', trigger: 'blur' }]
        }

      }
    },
    computed: {
    },
    watch: {
      dialogFormVisible: function() {
        this.resetTemp()
      },
      temp: {

        handler(newVal, oldVal) {},
        immediate: true,
        deep: true
      }
    },
    created() {
    },
    destroyed() {

    },
    methods: {

      handleClose(done) {
        if (this.temp.username == this.oldtemp.username &&
                this.temp.password == this.oldtemp.password &&
                this.temp.interface_name == this.oldtemp.interface_name &&
                this.temp.url == this.oldtemp.url &&
                this.temp.status == this.oldtemp.status)
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
      resetTemp() {
        this.temp = {
          id: 0,
          username: '',
          password: '',
          interface_name: '',
          url: '',
          status: 1,
        }
      },
      handleCreate() {
        this.dialogFormVisible = true
        this.btnLoading = false
        this.currentIndex = -1
        this.oldtemp.username = ''
        this.oldtemp.password = ''
        this.oldtemp.interface_name = ''
        this.oldtemp.url = ''
        this.oldtemp.status = 1
        this.$nextTick(() => {
          this.$refs['dataForm'].clearValidate()
        })
      },
      handleUpdate(id) {
        this.dialogFormVisible = true
        this.btnLoading = false
        const _this = this
        getinfo(id).then(response => {
          if (response.status === 1) {
            _this.temp.id = response.data.id
            _this.temp.username = response.data.username
            _this.temp.password = response.data.password
            _this.temp.interface_name = response.data.interface_name
            _this.temp.url = response.data.url
            _this.temp.status = response.data.status

            _this.oldtemp.username = response.data.username
            _this.oldtemp.password = response.data.password
            _this.oldtemp.interface_name = response.data.interface_name
            _this.oldtemp.url = response.data.url
            _this.oldtemp.status = response.data.status
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
            }).catch(() => {
              this.btnLoading = false
            })
          } else {
            this.btnLoading = false
          }
        })
      }
    }
  }
</script>
