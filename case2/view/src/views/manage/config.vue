<template>
  <div class="app-container">
    <el-form ref="dataForm" :model="temp" label-position="left" label-width="150px" style="width: 50%; ">
      <el-form-item label="youtubeApi_key" prop="youtubeApi_key">
        <el-input v-model="temp.youtubeApi_key" />
      </el-form-item>
      <el-form-item label="ffmpeg" prop="ffmpeg">
        <el-input v-model="temp.ffmpeg" />
      </el-form-item>
      <el-form-item label="ffprobe" prop="ffprobe">
        <el-input v-model="temp.ffprobe" clearable />
      </el-form-item>
      <el-form-item label="yt_dlp" prop="yt_dlp">
        <el-input v-model="temp.yt_dlp" clearable />
      </el-form-item>
      <el-form-item label="accessKeyId" prop="accessKeyId">
        <el-input v-model="temp.accessKeyId" clearable />
      </el-form-item>
      <el-form-item label="accessKeySecret" prop="accessKeySecret">
        <el-input v-model="temp.accessKeySecret" clearable />
      </el-form-item>
      <el-form-item label="bucket" prop="bucket">
        <el-input v-model="temp.bucket" clearable />
      </el-form-item>
    </el-form>
    <el-button :loading="btnLoading" type="primary" @click="saveData()">保存</el-button>
  </div>
</template>

<script>
import { getConfig, editConfig } from '@/api/user'

export default {
  name: 'Config',
  components: {  },
  data() {
    return {
      btnLoading: false,
      temp: {
        youtubeApi_key: '',
        ffmpeg: '',
        ffprobe: '',
        yt_dlp: '',
        accessKeyId: '',
        accessKeySecret: '',
        bucket: '',
      },
    }
  },
  watch: {
    temp: {
      handler(newVal, oldVal) {},
      immediate: true,
      deep: true
    }
  },
  created() {
    this.getConfig()
  },
  destroyed() {

  },
  methods: {
    getConfig() {
      getConfig().then(response => {
        if (response.status === 1){
          this.temp = response.data
        }
      })
    },

    saveData() {
      this.btnLoading = true
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const _this = this
          editConfig(this.temp).then(response => {
            if (response.status === 1) {
              _this.$message.success(response.msg)
            } else {
              _this.$message.error(response.msg)
            }
            _this.btnLoading = false
          }).catch((error) => {
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
