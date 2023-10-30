<template>
  <div class="app-container">
    <!-- 搜索 -->
    <div  v-if="showSearch" class="filter-container">
      <el-form :inline="true" :model="listQuery" class="form-inline">
        <el-form-item label="">
          <el-input v-model="listQuery.link" placeholder="视频链接" clearable size="small" />
        </el-form-item>
        <el-form-item label="">
          <el-input v-model="listQuery.title" placeholder="视频标题" clearable size="small" />
        </el-form-item>
        <el-form-item label="">
          <el-select v-model="listQuery.classify" placeholder="分类" clearable size="small">
            <el-option label="youtube" value="youtube" />
            <el-option label="bilibili" value="bilibili" />
            <el-option label="douyin" value="douyin" />
            <el-option label="kuaishou" value="kuaishou" />
          </el-select>
        </el-form-item>
        <el-form-item label="">
          <el-select v-model="listQuery.status" placeholder="状态" clearable size="small">
            <el-option label="全部" value="-1" />
            <el-option label="已发布" value="1" />
            <el-option label="未发布" value="0" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button v-waves type="primary" icon="el-icon-search" size="small" @click="handleFilter">搜索</el-button>
        </el-form-item>
        <el-form-item>
          <el-button v-waves type="warning" icon="el-icon-refresh" size="small" @click="handleFilterClear">重置</el-button>
        </el-form-item>
      </el-form>
    </div>

    <!-- 下载 -->
    <div class="filter-container">
      <el-form :inline="true" :model="listQuery" class="form-inline">
        <el-form-item label="">
          <el-input v-model="listQuery.link" placeholder="视频链接" clearable size="medium" style="min-width:745px">
            <template slot="prepend">Youtube/B站</template>
          </el-input>
        </el-form-item>
        <el-form-item>
          <el-button v-waves type="success" icon="el-icon-plus" size="medium" @click="addLink">添加</el-button>
        </el-form-item>
        <el-form-item>
          <el-button v-waves type="warning" icon="el-icon-refresh" size="medium" @click="addClear">重置</el-button>
        </el-form-item>
      </el-form>
    </div>

    <!-- 操作 -->
    <el-row style="margin-bottom: 10px;">
      <el-col :xs="24" :sm="24" :lg="24">
        <el-tooltip content="刷新" placement="top">
          <el-button v-waves type="warning" icon="el-icon-refresh" circle @click="handleFilterClear" />
        </el-tooltip>
        <el-tooltip content="搜索" placement="top">
          <el-button v-waves type="primary" icon="el-icon-search" circle @click="showSearch = !showSearch" />
        </el-tooltip>
        <el-tooltip content="删除" placement="top">
          <el-button v-waves :loading="deleting" :disabled="buttonDisabled" type="danger" icon="el-icon-delete" circle @click="handleDeleteAll()" />
        </el-tooltip>
      </el-col>
    </el-row>

    <!-- 视频 -->
    <el-dialog
            :title="videoUrl"
            :visible.sync="dialogVisible"
            width="30%"
            :before-close="handleClose">
      <video :src="videoUrl" controls autoplay class="video" width="100%"></video>

      <canvas id="myCanvas" width="343" height="200"></canvas>

      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="cutPicture">截 图</el-button>
      </span>

    </el-dialog>

    <!-- 表格 -->
    <el-table
            :key="tableKey"
            v-loading="listLoading"
            :data="list"
            border
            fit
            highlight-current-row
            style="width: 100%;"
            @selection-change="handleSelectionChange"
    >
      <el-table-column type="selection" width="55" />
      <el-table-column label="ID" width="50px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.$index + 1 }}</span>
        </template>
      </el-table-column>

      <el-table-column label="视频链接" width="350px" align="center" :show-overflow-tooltip="true">
        <template slot-scope="scope">
          <el-link type="primary" :underline="false" :href="scope.row.link" target ="_blank">{{ scope.row.link }}</el-link>
        </template>
      </el-table-column>

      <el-table-column label="分类" width="80px" align="center">
        <template slot-scope="scope">
          <span>{{scope.row.classify}}</span>
        </template>
      </el-table-column>

      <el-table-column label="视频大小(M)" width="80px" align="center">
        <template slot-scope="scope">
          <span>{{scope.row.size}}</span>
        </template>
      </el-table-column>

      <el-table-column label="视频时长" width="80px" align="center">
        <template slot-scope="scope">
          <span class="link-type" @click="videoEvent(scope.row.url)">{{scope.row.video_length}}</span>
        </template>
      </el-table-column>

      <el-table-column label="视频下载用时(s)" width="80px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.mp4_download_time }}</span>
        </template>
      </el-table-column>

      <el-table-column label="视频转音频用时(s)" width="80px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.mp3_transcription_time	 }}</span>
        </template>
      </el-table-column>

      <el-table-column label="音频上传oss用时(s)" width="80px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.oss_upload_time }}</span>
        </template>
      </el-table-column>

      <el-table-column label="音频识别用时(s)" width="80px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.mp3_translate_time }}</span>
        </template>
      </el-table-column>

      <el-table-column label="文字长度" width="80px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.text_length }}</span>
        </template>
      </el-table-column>

      <el-table-column label="视频标题" min-width="250px" align="center" :show-overflow-tooltip="true">
        <template slot-scope="scope">
          <span>{{scope.row.title}}</span>
        </template>
      </el-table-column>

      <el-table-column label="状态" width="80px" align="center">
        <template slot-scope="scope">
          <span :class="{'el-icon-success text-green':scope.row.status == 1,'el-icon-remove-outline text-grey':scope.row.status == 0}">{{ scope.row.status | statusFilter }}</span>
        </template>
      </el-table-column>

      <el-table-column label="操作" align="center" width="150px" class-name="small-padding fixed-width" fixed="right">
        <template slot-scope="scope">
          <el-tooltip content="一键下载" placement="top">
            <el-button v-waves type="success" circle icon="el-icon-download" @click="download(scope.row)" />
          </el-tooltip>
          <el-tooltip content="编辑发布" placement="top">
            <el-button v-waves type="primary" icon="el-icon-edit-outline" circle @click="handleUpdate(scope.$index,scope.row.id)" />
          </el-tooltip>
          <el-tooltip content="删除" placement="top">
            <el-button v-waves :loading="scope.row.delete" type="danger" icon="el-icon-delete" circle @click="handleDelete(scope.$index,scope.row.id)" />
          </el-tooltip>
        </template>
      </el-table-column>
    </el-table>

    <!-- 分页 -->
    <div class="pagination-container">
      <el-pagination v-show="total>0" :current-page="listQuery.page" :page-sizes="[10,20,30, 50]" :page-size="listQuery.psize" :total="total" background layout="total, sizes, prev, pager, next, jumper" @size-change="handleSizeChange" @current-change="handleCurrentChange" />
    </div>

    <!-- 表单 -->
    <detailForm ref="fromDetail" @updateRow="updateRow" />

    <!-- 提示框 -->
    <div class="profPrompt" :style="{display: visible}">
      <div>{{message}}</div>
    </div>

  </div>


</template>

<script>
  import {download, saveLink, getList, ytdel, delAll} from '@/api/youtube'
  import waves from '@/directive/waves'
  import { pickerOptions, getArrByKey } from '@/utils'
  import detailForm from './youtube/form'

  export default {
    name: 'Youtube',
    components: { detailForm },
    directives: {
      waves
    },
    filters: {
      statusFilter(status) {
        const statusMap = {
          0: '未发布',
          1: '已发布'
        }
        return statusMap[status]
      }
    },
    data() {
      return {
        imgsrc: '',
        videoUrl: '',
        dialogVisible: false,
        tableKey: 0,
        list: null,
        total: null,
        selectedRows: null,
        listLoading: true,
        showSearch: false,
        listQuery: {
          page: 1,
          psize: 10,
          status: '-1',
          link: '',
          title: '',
          classify: '',
        },
        buttonDisabled: true,
        deleting: false,
        dateTime: '',
        pickerOptions: pickerOptions,
        currentIndex: -1,

        visible: 'none',
        message: "",

      }
    },
    watch: {
      dateTime: function(val) {
        this.listQuery.start_time = val[0]
        this.listQuery.end_time = val[1]
      }
    },
    created() {
      this.fetchList()
    },
    methods: {
      cutPicture() {
        let video = document.querySelector('video')
        let canvas = document.getElementById('myCanvas')
        canvas.getContext('2d').drawImage(video, 0, 0, 343, 200)
      },

      handleClose() {
        this.videoUrl = ''
        this.dialogVisible = false
      },

      addLink() {
        const _this = this
        saveLink(this.listQuery).then(response => {
          if (response.status === 1) {
            _this.$message.success(response.msg)
          } else {
            _this.$message.error(response.msg)
          }
          this.addClear()
          this.resetList()
        })
      },

      videoEvent(url) {
        this.dialogVisible = true;
        this.videoUrl = url;
      },

      download(list) {
        this.$notify.success('正在下载，请等待！')
        this.visible = "block";
        this.message = "正在下载，请等待！"
        const _this = this
        download(list).then(response => {
          if (response.status === 1 || response.status === undefined){
            _this.$notify.success('下载成功！')
          }else {
            _this.$notify.error('下载失败！请检查视频链接是否准确!')
          }
          this.visible = "none";
          this.resetList()
        }).catch(() => {
          this.visible = "none";
        })
      },

      addClear() {
        this.listQuery = {
          link: '',
        }
      },

      resetList(){
        getList().then(response => {
          this.list = response.data.data
          this.total = response.data.total
          this.listLoading = false
        })
      },

      fetchList() {
        this.listLoading = true
        getList(this.listQuery).then(response => {
          this.list = response.data.data
          this.total = response.data.total
          this.listLoading = false
        })
      },
      handleFilter() {
        this.listQuery.page = 1
        this.fetchList()
      },
      handleFilterClear() {
        this.listQuery = {
          page: 1,
          psize: 10,
          status: '-1',
          link: '',
          title: '',
          classify: '',
        }
        this.dateTime = ''
        this.fetchList()
      },
      handleSizeChange(val) {
        this.listQuery.psize = val
        this.fetchList()
      },
      handleCurrentChange(val) {
        this.listQuery.page = val
        this.fetchList()
      },
      handleSelectionChange(val) {
        if (val.length > 0) {
          this.buttonDisabled = false
        } else {
          this.buttonDisabled = true
        }
        this.selectedRows = val
      },
      handleUpdate(index, id) {
        this.currentIndex = index
        this.$refs.fromDetail.handleUpdate(id)
      },
      updateRow(temp) {
        if (this.currentIndex >= 0 && temp.id > 0) {
          this.list.splice(this.currentIndex, 1, temp)
        } else {
          if (this.list.length >= 10) {
            this.list.pop()
          }
          this.list.unshift(temp)
          this.total = this.total + 1
        }
        this.currentIndex = -1
        this.fetchList()
      },
      handleDelete(index, id) {
        const _this = this
        this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          _this.$set(_this.list[index], 'delete', true)
          ytdel(id).then(response => {
            if (response.status === 1) {
              _this.list.splice(index, 1)
              _this.total = _this.total - 1
              _this.$notify.success(response.msg)
            } else {
              _this.$notify.error(response.msg)
            }
            _this.$set(_this.list[index], 'delete', false)

          }).catch((error) => {
            _this.$set(_this.list[index], 'delete', false)
          })
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消删除'
          })
        })
      },
      handleDeleteAll() {
        const _this = this
        if (this.selectedRows.length > 0) {
          this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
          }).then(() => {
            _this.deleting = true
            const ids = getArrByKey(_this.selectedRows, 'id')
            const idstr = ids.join(',')
            delAll({ ids: idstr }).then(response => {
              if (response.status === 1) {
                const delindex = []

                _this.list.forEach(function(item, index, input) {
                  if (ids.indexOf(item.id) > -1) {
                    delindex.push(index)
                  }
                })
                for (let i = delindex.length - 1; i >= 0; i--) {
                  _this.list.splice(delindex[i], 1)
                }
                _this.total = _this.total - delindex.length
                _this.$message.success(response.msg)
              } else {
                _this.$message.error(response.msg)
              }
              _this.deleting = false

            }).catch((error) => {
              _this.deleting = false
            })
          }).catch(() => {
            this.$message({
              type: 'info',
              message: '已取消删除'
            })
          })
        } else {
          _this.$message.error('请选择要删除的数据')
        }
      }
    }
  }
</script>
<style rel="stylesheet/scss" lang="scss">
  .text-red{
    color: red;
    cursor: pointer;
  }
  .text-green{
    color: green;
    cursor: pointer;
  }
  .profPrompt{
    display: flex;
    align-items: center;
    padding: 12px 18px;
    overflow: hidden;
    color: #33d57b;
    position: absolute;
    background: #e7faf0;
    border-radius: 5px;
    top: 30px;
    left: 90%;
    font-size: 15px;
    font-weight: 700;
    opacity: 1;
    text-align: center;
    transform: translate(-50%, -50%);
  }

   /*/deep/ .el-dialog {*/
   /*  margin: 0 auto !important;*/
   /*  position: relative;*/
   /*  .el-dialog__header{*/
   /*    position: absolute;*/
   /*    left: 0;*/
   /*    top: 0;*/
   /*    right: 0;*/
   /*    width: 100%;*/
   /*    height: 60px;*/
   /*    z-index: 1;*/
   /*  }*/
   /*  .el-dialog__body {*/
   /*    width: 100%;*/
   /*    overflow: hidden;*/
   /*    overflow-y: auto;*/
   /*    max-height: 90vh;*/
   /*    padding-top: 80px;*/
   /*    padding-bottom: 100px;*/
   /*    z-index: 1;*/
   /*  }*/
   /*  .el-dialog__footer{*/
   /*    position: absolute;*/
   /*    left: 0;*/
   /*    bottom: 0;*/
   /*    right: 0;*/
   /*    width: 100%;*/
   /*    height: 80px;*/
   /*    z-index: 1;*/
   /*  }*/
   /*}*/

</style>
