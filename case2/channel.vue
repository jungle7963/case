<template>
  <div class="app-container">
    <!-- 搜索 -->
    <div class="filter-container">
      <el-form :inline="true" :model="listQuery" class="form-inline">
        <el-form-item label="">
          <el-input v-model="listQuery.channelId" placeholder="频道ID" clearable size="small" />
        </el-form-item>
        <el-form-item label="">
          <country-selector placeholder="国家/地区" v-model="listQuery.country"></country-selector>
        </el-form-item>
        <el-form-item label="">
          <el-date-picker v-model="dateTime" :picker-options="pickerOptions" type="daterange" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期" value-format="yyyy-MM-dd" align="right" clearable size="small" />
        </el-form-item>
        <el-form-item>
          <el-button v-waves type="primary" icon="el-icon-search" size="small" @click="handleFilter">搜索</el-button>
        </el-form-item>
        <el-form-item>
          <el-button v-waves type="warning" icon="el-icon-refresh" size="small" @click="handleFilterClear">重置</el-button>
        </el-form-item>
      </el-form>
    </div>

    <!-- 操作 -->
    <el-row style="margin-bottom: 10px;">
      <el-col :xs="24" :sm="24" :lg="24">
        <el-tooltip content="刷新" placement="top">
          <el-button v-waves type="warning" icon="el-icon-refresh" circle @click="handleFilterClear" />
        </el-tooltip>

        <el-tooltip content="查看人数降序" placement="top">
          <el-button v-waves type="success" icon="el-icon-refresh" circle @click="sort" />
        </el-tooltip>
        <el-tooltip content="删除" placement="top">
          <el-button v-waves :loading="deleting" :disabled="buttonDisabled" type="danger" icon="el-icon-delete" circle @click="handleDeleteAll()" />
        </el-tooltip>
      </el-col>
    </el-row>

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
      <el-table-column type="selection" width="45" />
      <el-table-column label="ID" align="center" width="50px" fixed>
        <template slot-scope="scope">
          <span>{{ scope.$index + 1 }}</span>
        </template>
      </el-table-column>
      <el-table-column label="频道链接" width="450px" align="center">
        <template slot-scope="scope">
          <el-link type="primary" :underline="false" :href="scope.row.link" target ="_blank">{{ scope.row.link }}</el-link>
        </template>
      </el-table-column>
      <el-table-column label="频道ID" width="250px" align="center">
        <template slot-scope="scope">
          <span>{{scope.row.channelId}}</span>
        </template>
      </el-table-column>
      <el-table-column label="频道标题" width="130px" align="center" :show-overflow-tooltip="true">
        <template slot-scope="scope">
          <span>{{scope.row.channelTitle}}</span>
        </template>
      </el-table-column>
      <el-table-column label="查看人数" width="100px" align="center">
        <template slot-scope="scope">
          <span>{{scope.row.viewCount}}</span>
        </template>
      </el-table-column>
      <el-table-column label="订阅数量" width="90px" align="center">
        <template slot-scope="scope">
          <span>{{scope.row.subscriberCount}}</span>
        </template>
      </el-table-column>
      <el-table-column label="视频数量" width="77px" align="center">
        <template slot-scope="scope">
          <span>{{scope.row.videoCount}}</span>
        </template>
      </el-table-column>
      <el-table-column label="国家/地区" width="90px" align="center">
        <template slot-scope="scope">
          <span>{{scope.row.country}}</span>
        </template>
      </el-table-column>
      <el-table-column label="发布时间" width="100px" align="center">
        <template slot-scope="scope">
          <span>{{scope.row.publishedAt}}</span>
        </template>
      </el-table-column>
      <el-table-column label="头像" width="70px" align="center">
        <template slot-scope="scope">
          <span class="link-type" @click="handleImg(scope.row.img)"><img :src="scope.row.img" width="40" height="40"></span>
        </template>
      </el-table-column>
      <el-table-column label="创建时间" width="160px" align="center">
        <template slot-scope="scope">
          <span>{{scope.row.create_time}}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" width="100px" class-name="small-padding fixed-width" fixed="right">
        <template slot-scope="scope">
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

  </div>
</template>

<script>
  import { getList, del, delAll } from '@/api/channel'
  import waves from '@/directive/waves'
  import { pickerOptions, getArrByKey } from '@/utils'
  import CountrySelector from 'vue-country-selector'
  import 'vue-country-selector/dist/countryselector.css'
  import openWindow from '@/utils/open-window'

  export default {
    name: 'Channel',
    components: {CountrySelector},
    directives: {
      waves
    },
    data() {
      return {
        tableKey: 0,
        list: null,
        total: null,
        selectedRows: null,
        listLoading: true,
        listQuery: {
          page: 1,
          psize: 10,
          link: '',
          country: '',
          start_time: '',
          end_time: ''
        },
        buttonDisabled: true,
        deleting: false,
        currentIndex: -1,
        dateTime:'',
        pickerOptions: pickerOptions
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
      fetchList() {
        this.listLoading = true
        getList(this.listQuery).then(response => {
          this.list = response.data.data
          this.total = response.data.total
          this.listLoading = false
        })
      },
      sort() {
        this.listLoading = true
        getList(this.listQuery).then(response => {
          const arr = response.data.data
          this.list = arr.sort(this.Ascending('viewCount'))
          this.total = response.data.total
          this.listLoading = false
        })
      },
      Ascending(i){
        return function(a,b) {
          return b[i] - a[i]
        }
      },
      handleImg(img) {
        openWindow(img, '图片预览', '500', '400')
      },
      handleFilter() {
        this.listQuery.page = 1
        this.fetchList()
      },
      handleFilterClear() {
        this.listQuery = {
          page: 1,
          psize: 10,
          link: '',
          country: '',
          start_time: '',
          end_time: ''
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
      handleDelete(index, id) {
        const _this = this
        this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          _this.$set(_this.list[index], 'delete', true)
          del(id).then(response => {
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
      },
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
</style>
