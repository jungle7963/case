<template>
  <div class="app-container">
    <!-- 搜索 -->
    <div class="filter-container">
      <el-form :inline="true" :model="listQuery" class="form-inline">
        <el-form-item label="">
          <el-input v-model="listQuery.username" placeholder="发布者" clearable size="small" />
        </el-form-item>
        <el-form-item label="">
          <el-input v-model="listQuery.title" placeholder="视频标题" clearable size="small" />
        </el-form-item>
        <el-form-item label="">
          <el-input v-model="listQuery.wordpress_url" placeholder="文章链接" clearable size="small" />
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
        <el-tooltip content="添加发布" placement="top">
          <el-button v-waves type="success" icon="el-icon-plus" circle @click="handleCreate" />
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
      <el-table-column label="ID" align="center" width="170px" fixed>
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="发布者" width="100px" align="center">
        <template slot-scope="scope">
          <span>{{scope.row.username}}</span>
        </template>
      </el-table-column>
      <el-table-column label="视频标题" min-width="100px" align="center" :show-overflow-tooltip="true">
        <template slot-scope="scope">
          <span>{{scope.row.title}}</span>
        </template>
      </el-table-column>
      <el-table-column label="文章链接" width="430px" align="center" :show-overflow-tooltip="true">
        <template slot-scope="scope">
            <el-link type="primary" :underline="false" :href="scope.row.wordpress_url" target ="_blank">{{ scope.row.wordpress_url }}</el-link>
        </template>
      </el-table-column>
      <el-table-column label="创建时间" width="200px" align="center">
        <template slot-scope="scope">
          <span>{{scope.row.create_time}}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" width="100px" class-name="small-padding fixed-width" fixed="right">
        <template slot-scope="scope">
          <el-tooltip content="查看" placement="top">
            <el-button v-waves type="primary" icon="el-icon-edit-outline" circle @click="handleUpdate(scope.$index,scope.row.id)" />
          </el-tooltip>
        </template>
      </el-table-column>
    </el-table>

    <!-- 分页 -->
    <div class="pagination-container">
      <el-pagination v-show="total>0" :current-page="listQuery.page" :page-sizes="[10,20,30, 50]" :page-size="listQuery.psize" :total="total" background layout="total, sizes, prev, pager, next, jumper" @size-change="handleSizeChange" @current-change="handleCurrentChange" />
    </div>

    <cdetailForm ref="fromDetail" @updateRow="updateRow" />

  </div>
</template>

<script>
  import { getList } from '@/api/wordpress'
  import waves from '@/directive/waves'

  import { pickerOptions, getArrByKey } from '@/utils'
  import cdetailForm from './wordpress/form'


  export default {
    name: 'Wordpress',
    components: { cdetailForm },
    directives: {
      waves
    },
    filters: {},
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
          title: '',
          username: '',
          wordpress_url: '',
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
      handleFilter() {
        this.listQuery.page = 1
        this.fetchList()
      },
      handleFilterClear() {
        this.listQuery = {
          page: 1,
          psize: 10,
          link: '',
          title: '',
          username: '',
          wordpress_url: '',
          start_time: '',
          end_time: ''
        }
        this.fetchList()
      },
      updateRow() {
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
      handleCreate() {
        this.$refs.fromDetail.handleCreate()
      },
      handleUpdate(index, id) {
        this.currentIndex = index
        this.$refs.fromDetail.handleUpdate(id)
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
