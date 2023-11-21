<template>
  <div class="app-container">
    <!-- 操作 -->
    <el-row style="margin-bottom: 10px;">
      <el-col :xs="24" :sm="24" :lg="24">
        <el-tooltip content="刷新" placement="top">
          <el-button v-waves type="warning" icon="el-icon-refresh" circle @click="handleFilterClear" />
        </el-tooltip>
        <el-button style="margin-left: 30px" size="mini" :type=btn_status @click="getVersion">{{btn_version}}</el-button>
        <span style="margin-left: 30px">当前数据库版本 {{current_version}}</span>
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
    >
      <el-table-column label="ID" align="center" width="180">
        <template slot-scope="scope">
          <span>{{ scope.$index + 1 }}</span>
        </template>
      </el-table-column>
      <el-table-column label="用户名" align="center" width="150px">
        <template slot-scope="scope">
          <span>{{ scope.row.username }}</span>
        </template>
      </el-table-column>
      <el-table-column label="角色" align="center" width="180px">
        <template slot-scope="scope">
          <span>{{ scope.row.roles }}</span>
        </template>
      </el-table-column>
      <el-table-column label="更新时间" width="200px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.create_time|formTime('{y}-{m}-{d} {h}:{i}:{s}') }}</span>
        </template>
      </el-table-column>
      <el-table-column label="更新内容" min-width="150px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.update_content }}</span>
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
import { getUpdateList } from '@/api/log'
import { editVersion } from '@/api/user'
import waves from '@/directive/waves'
import { parseTime, pickerOptions } from '@/utils'

export default {
  name: 'Log',
  components: { },
  directives: {
    waves
  },
  filters: {
    formTime(format){
      return parseTime(format)
    }
  },
  data() {
    return {
      tableKey: 0,
      list: null,
      total: null,
      listLoading: true,
      showSearch: false,
      listQuery: {
        page: 1,
        psize: 10,
        username: '',
        roles: '',
        start_time: '',
        end_time: ''
      },
      dateTime: '',
      pickerOptions: pickerOptions,
      btn_version: '',
      current_version: '',
      btn_status: 'success'
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
    getVersion() {
      const _this = this
      if (this.btn_version != '已是最新版本') {
        this.btn_status = 'danger'
        this.$alert('当前数据库版本不是最新，请更新！', '更新提醒', {
          confirmButtonText: '更新',
          callback: action => {
            if (action == 'confirm'){
              editVersion().then(res => {
                if (res.status == 1) {
                  _this.$message.success(res.msg)
                  _this.btn_status = 'success'
                }else {
                  _this.$message.error(res.msg)
                }
              }).finally(() => {
                this.fetchList()
              })
            }
          }
        })
      }
    },
    fetchList() {
      this.listLoading = true
      getUpdateList(this.listQuery).then(response => {
        if (response.data.data.length == 0) {
          this.fetchList()
        }else {
          this.current_version = response.data.data[0]['version']
          this.btn_version = response.data.data[0]['btn_version']
          this.list = response.data.data
          this.total = response.data.total
          this.listLoading = false
          this.getVersion()
        }
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
        username: '',
        roles: '',
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
    }
  }
}
</script>
