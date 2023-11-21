<template>
  <div class="dashboard-editor-container">
<!--    <github-corner class="github-corner" />-->

    <panel-group @handleSetLineChartData="handleSetLineChartData" />

      <div style="margin-bottom:32px;" v-if="showSelect">
          <el-select v-model="username" placeholder="选择操作员" clearable size="small">
              <el-option @click.native="getOperator" v-for="item in usernames" :key="item" :label="item" :value="item" />
          </el-select>
      </div>

    <el-row style="background:#fff;padding:16px 16px 0;margin-bottom:32px;">
      <line-chart :chart-data="lineChartData" />
    </el-row>

<!--    <el-row :gutter="32">-->
<!--      <el-col :xs="24" :sm="24" :lg="8">-->
<!--        <div class="chart-wrapper">-->
<!--          <raddar-chart />-->
<!--        </div>-->
<!--      </el-col>-->
<!--      <el-col :xs="24" :sm="24" :lg="8">-->
<!--        <div class="chart-wrapper">-->
<!--          <pie-chart />-->
<!--        </div>-->
<!--      </el-col>-->
<!--      <el-col :xs="24" :sm="24" :lg="8">-->
<!--        <div class="chart-wrapper">-->
<!--          <bar-chart />-->
<!--        </div>-->
<!--      </el-col>-->
<!--    </el-row>-->
  </div>
</template>

<script>
// import GithubCorner from '@/components/GithubCorner'
import PanelGroup from './components/PanelGroup'
import LineChart from './components/LineChart'
import { getCount, getVersion, editVersion } from '@/api/user'

// import RaddarChart from './components/RaddarChart'
// import PieChart from './components/PieChart'
// import BarChart from './components/BarChart'

const lineChartData = {
  newVisitis: {
    expectedData: [],
    actualData: [],
    x: [],
    username: [],
    releaseTotal: ''
  },
  // messages: {
  //   expectedData: [200, 192, 120, 144, 160, 130, 140],
  //   actualData: [180, 160, 151, 106, 145, 150, 130]
  // },
  // purchases: {
  //   expectedData: [80, 100, 121, 104, 105, 90, 100],
  //   actualData: [120, 90, 100, 138, 142, 130, 130]
  // },
  // shoppings: {
  //   expectedData: [130, 140, 141, 142, 145, 150, 160],
  //   actualData: [120, 82, 91, 154, 162, 140, 130]
  // }
}

export default {
  name: 'DashboardAdmin',
  components: {
    // GithubCorner,
    PanelGroup,
    LineChart,
    // RaddarChart,
    // PieChart,
    // BarChart
  },
  data() {
    return {
      lineChartData: lineChartData.newVisitis,
      usernames: null,
      username: '',
      showSelect: false,
    }
  },
    created() {
        this.getVersion()
        this.fetchList()
    },
    methods: {
        getVersion() {
            const _this = this
            getVersion().then(response => {
                if (response.code === 10034){
                    this.$alert('当前数据库版本不是最新，请更新！', '更新提醒', {
                        confirmButtonText: '更新',
                        callback: action => {
                            if (action == 'confirm'){
                                editVersion().then(res => {
                                    if (res.status == 1) {
                                        _this.$message.success(res.msg)
                                    }else {
                                        _this.$message.error(res.msg)
                                    }
                                })
                            }
                        }
                    })
                }
            })
        },

        fetchList() {
            getCount({username:this.username}).then(response => {
                if (response.status === 1){
                    if (response.data.usernameCount != undefined){
                        this.showSelect = true
                        this.lineChartData['releaseTotal'] = '总量';
                    }
                    this.lineChartData['expectedData'] = response.data.releaseCounts;
                    this.lineChartData['actualData'] = response.data.releaseCount;
                    this.lineChartData['x'] = response.data.times;
                    this.lineChartData['username'] = response.data.username;
                    this.usernames = response.data.usernames;
                    this.username = response.data.username;
                }
            })
        },
        getOperator() {
            this.fetchList()
        },
        handleSetLineChartData(type) {
          this.lineChartData = lineChartData[type]
        }
  }
}
</script>

<style lang="scss" scoped>
.dashboard-editor-container {
  padding: 32px;
  background-color: rgb(240, 242, 245);
  position: relative;

  .github-corner {
    position: absolute;
    top: 0px;
    border: 0;
    right: 0;
  }

  .chart-wrapper {
    background: #fff;
    padding: 16px 16px 0;
    margin-bottom: 32px;
  }
}

@media (max-width:1024px) {
  .chart-wrapper {
    padding: 8px;
  }
}
</style>
