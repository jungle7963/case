<template>
  <div class="app-container">
    <!-- 综合查询 -->
    <div class="filter-container">
      <el-form :inline="true" :model="listQuery" class="form-inline">
        <el-form-item label="查询视频或频道">
        </el-form-item>
        <el-form-item label="">
          <el-cascader
                  :options="options"
                  :props="{ checkStrictly: true }"
                  placeholder="类型"
                  v-model="listQuery.type"
                  clearable></el-cascader>
        </el-form-item>
        <el-form-item label="">
          <el-input v-model="listQuery.count" placeholder="搜索条数" clearable size="small" />
        </el-form-item>
        <el-form-item label="">
          <el-input v-model="listQuery.channelId" placeholder="频道ID" clearable size="small" />
        </el-form-item>
        <el-form-item label="">
          <el-input v-model="listQuery.q" placeholder="关键词" clearable size="small" />
        </el-form-item>
        <el-form-item label="">
          <country-selector placeholder="国家/地区" v-model="listQuery.regionCode"></country-selector>
        </el-form-item>
        <el-form-item label="">
          <el-select v-model="listQuery.order" placeholder="排序方式" clearable size="small">
            <el-option label="观看人数降序" value="viewCount" />
            <el-option label="发布时间降序" value="date" />
            <el-option label="评级降序" value="rating" />
            <el-option label="上传视频数量降序" value="videoCount" />
            <el-option label="标题字母升序" value="title" />
          </el-select>
        </el-form-item>
        <el-form-item label="">
          <el-date-picker v-model="dateTime" :picker-options="pickerOptions" type="daterange" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期" value-format="yyyy-MM-dd" align="right" clearable size="small" />
        </el-form-item>
        <el-form-item>
          <el-button v-waves type="primary" icon="el-icon-search" size="small" @click="query">搜索</el-button>
        </el-form-item>
        <el-form-item>
          <el-button v-waves type="warning" icon="el-icon-refresh" size="small" @click="handleFilterClear">重置</el-button>
        </el-form-item>
      </el-form>
    </div>

    <!-- 视频ID查询 -->
    <div  v-if="showSearch" class="filter-container">
      <el-form :inline="true" :model="listQuery" class="form-inline">
        <el-form-item label="查询视频">
        </el-form-item>
        <el-form-item label="" style="margin-left: 40px">
          <el-input v-model="listQuery.vid" placeholder="youtube视频ID" clearable size="small" />
        </el-form-item>
        <el-form-item>
          <el-button v-waves type="primary" icon="el-icon-search" size="small" @click="serach">搜索</el-button>
        </el-form-item>
      </el-form>
    </div>

    <el-row style="margin-bottom: 10px;">
      <el-col :xs="24" :sm="24" :lg="24">
        <el-tooltip content="视频ID查询" placement="top">
          <el-button v-waves type="primary" icon="el-icon-search" circle @click="showSearch = !showSearch" />
        </el-tooltip>
        <el-tooltip content="批量保存" placement="top">
          <el-button v-waves :disabled="buttonDisabled" type="primary" icon="el-icon-check" circle @click="saveAll()" />
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
            v-if="videoView"
    >
      <el-table-column type="selection" width="45" />
      <el-table-column label="ID" width="50px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.$index + 1 }}</span>
        </template>
      </el-table-column>
      <el-table-column label="视频链接" align="center" width="340px" >
        <template slot-scope="scope">
          <el-link type="primary" :underline="false" :href="scope.row.link" target ="_blank">{{ scope.row.link }}</el-link>
        </template>
      </el-table-column>
      <el-table-column label="频道ID" width="250px" align="center">
        <template slot-scope="scope">
          <el-link type="primary" :underline="false" :href="'https://www.youtube.com/channel/'+scope.row.channelId" target="_blank">{{ scope.row.channelId }}</el-link>
        </template>
      </el-table-column>
      <el-table-column label="频道标题" width="140px" align="center" :show-overflow-tooltip="true">
        <template slot-scope="scope">
          <span>{{ scope.row.channelTitle }}</span>
        </template>
      </el-table-column>
      <el-table-column label="观看人数" width="100px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.viewCount }}</span>
        </template>
      </el-table-column>
      <el-table-column label="点赞人数" width="80px" align="center" >
        <template slot-scope="scope">
          <span>{{ scope.row.likeCount }}</span>
        </template>
      </el-table-column>
      <el-table-column label="国家/地区" width="90px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.country }}</span>
        </template>
      </el-table-column>
      <el-table-column label="发布时间" width="100px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.publishedAt }}</span>
        </template>
      </el-table-column>
      <el-table-column label="图片" align="center" width="70px">
        <template slot-scope="scope">
          <span class="link-type" @click="handleImg(scope.row.img)"><img :src="scope.row.img" width="40" height="40"></span>
        </template>
      </el-table-column>
      <el-table-column label="视频标题" width="280px" align="center" :show-overflow-tooltip="true" >
        <template slot-scope="scope">
          <span>{{ scope.row.title }}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" width="130px" class-name="small-padding fixed-width" fixed="right">
        <template slot-scope="scope">
          <el-tooltip content="查看字幕" placement="top">
            <el-button v-waves type="primary" icon="el-icon-edit-outline" circle @click="handleUpdate(scope.$index,scope.row.link,scope.row.title)" />
          </el-tooltip>
          <el-tooltip content="保存" placement="top">
            <el-button v-waves type="primary" icon="el-icon-check" circle @click="save(scope.$index)" />
          </el-tooltip>
        </template>
      </el-table-column>
    </el-table>

    <el-table
            :key="tableKey"
            v-loading="listLoading"
            :data="list"
            border
            fit
            highlight-current-row
            style="width: 100%;"
            @selection-change="handleSelectionChange"
            v-if="channelView"
    >
      <el-table-column type="selection" width="45" />
      <el-table-column label="ID" width="50px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.$index + 1 }}</span>
        </template>
      </el-table-column>
      <el-table-column label="频道链接" width="450px" align="center">
        <template slot-scope="scope">
          <el-link type="primary" :underline="false" :href="'https://www.youtube.com/channel/'+scope.row.channelId" target="_blank">https://www.youtube.com/channel/{{scope.row.channelId}}</el-link>
        </template>
      </el-table-column>
      <el-table-column label="频道ID" width="250px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.channelId }}</span>
        </template>
      </el-table-column>
      <el-table-column label="频道标题" width="200px" align="center" :show-overflow-tooltip="true">
        <template slot-scope="scope">
          <span>{{ scope.row.channelTitle }}</span>
        </template>
      </el-table-column>
      <el-table-column label="观看人数" width="100px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.viewCount }}</span>
        </template>
      </el-table-column>
      <el-table-column label="订阅数量" width="100px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.subscriberCount }}</span>
        </template>
      </el-table-column>
      <el-table-column label="视频数量" width="100px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.videoCount }}</span>
        </template>
      </el-table-column>
      <el-table-column label="国家/地区" width="100px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.country }}</span>
        </template>
      </el-table-column>
      <el-table-column label="发布时间" width="100px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.publishedAt }}</span>
        </template>
      </el-table-column>
      <el-table-column label="作者头像" align="center" width="100px">
        <template slot-scope="scope">
          <span class="link-type" @click="handleImg(scope.row.img)"><img :src="scope.row.img" width="40" height="40"></span>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" width="70px" class-name="small-padding fixed-width" fixed="right">
        <template slot-scope="scope">
          <el-tooltip content="保存" placement="top">
            <el-button v-waves type="primary" icon="el-icon-check" circle @click="save(scope.$index)" />
          </el-tooltip>
        </template>
      </el-table-column>
    </el-table>

    <!-- 表单 -->
    <detailForm ref="fromDetail" @updateRow="updateRow" />

  </div>
</template>

<script>
  import { query, saveQuery, saveQueryAll, serach } from '@/api/youtube'
  import waves from '@/directive/waves'
  import { pickerOptions } from '@/utils'
  import CountrySelector from 'vue-country-selector'
  import 'vue-country-selector/dist/countryselector.css'
  import openWindow from '@/utils/open-window'

  import detailForm from './query/form'

  export default {
    name: 'Query',
    components: { detailForm, CountrySelector },
    directives: {
      waves
    },
    filters: {},
    data() {
      return {
        options: [
          {
            value: 'video',
            label: '视频',
            children: [{
              value: 'all',
              label: '全部',
              children: [{
                value: 'all',
                label: '全部',
              },{
                value: 'short',
                label: '小于4分钟'
              }, {
                value: 'medium',
                label: '4-20分钟'
              }, {
                value: 'long',
                label: '大于20分钟'
              }]
            },{
              value: 'closedCaption',
              label: '有字幕',
              children: [{
                value: 'all',
                label: '全部',
              },{
                value: 'short',
                label: '小于4分钟'
              }, {
                value: 'medium',
                label: '4-20分钟'
              }, {
                value: 'long',
                label: '大于20分钟'
              }]
            }, {
              value: 'none',
              label: '无字幕',
              children: [{
                value: 'all',
                label: '全部',
              },{
                value: 'short',
                label: '小于4分钟'
              }, {
                value: 'medium',
                label: '4-20分钟'
              }, {
                value: 'long',
                label: '大于20分钟'
              }]
            }]
          },{
            value: 'channel',
            label: '频道',
          }],
        showSearch: false,
        tableKey: 0,
        selectedRows: null,
        list: null,
        listLoading: false,
        listQuery: {
          page: 1,
          psize: 10,
          vid: '',
          count: '',
          order: '',
          start_time: '',
          end_time: '',
          q: '',
          channelId: '',
          regionCode: '',
          type: '',
        },
        dateTime:'',
        pickerOptions: pickerOptions,
        buttonDisabled: true,
        videoView: true,
        channelView: false,
      }
    },
    watch: {
      dateTime: function(val) {
        this.listQuery.start_time = val[0]
        this.listQuery.end_time = val[1]
      }
    },
    created() {
    },
    methods: {
      query() {
        this.list = null
        this.listLoading = true
        if (this.listQuery.count > 50){
          this.$message.warning('单次限制查询50条')
          this.listLoading = false
        }else {
          query(this.listQuery).then(response => {
            if (response.data[0]['genre'] === 'channel'){
              this.videoView = false;
              this.channelView = true;
            }else {
              this.videoView = true;
              this.channelView = false;
            }
            this.list = response.data
            this.listLoading = false
          }).catch(() => {
            this.listLoading = false
          })
        }
      },

      handleImg(img) {
        openWindow(img, '图片预览', '500', '400')
      },

      handleUpdate(index) {
        this.$refs.fromDetail.handleUpdate(this.list[index])
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

      serach() {
        this.listLoading = true
        serach(this.listQuery).then(response => {
          this.list = response.data
          this.listLoading = false
        }).catch(() => {
          this.listLoading = false
        })
      },

      save(index) {
        saveQuery(this.list[index]).then(response => {
          if (response.status === 1){
            this.$message.success(response.msg)
          }else {
            this.$message.error(response.msg)
          }
        })
      },

      handleSelectionChange(val) {
        if (val.length > 0) {
          this.buttonDisabled = false
        } else {
          this.buttonDisabled = true
        }
        this.selectedRows = val
      },

      saveAll(){
        const _this = this
        if (this.selectedRows.length > 0) {
          saveQueryAll(this.selectedRows).then(response => {
            if (response.status === 1){
              this.$message.success(response.msg)
            }else {
              this.$message.error(response.msg)
            }
          })
        } else {
          _this.$message.error('请选择要操作的数据')
        }

      },

      handleFilterClear() {
        this.listQuery = {
          page: 1,
          psize: 10,
          count: '',
          order: '',
          start_time: '',
          end_time: '',
          q: '',
          channelId: '',
          regionCode: '',
          type: '',
        }
        this.dateTime = ''
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
