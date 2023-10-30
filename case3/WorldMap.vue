<template>
  <div class="echarts" ref="echarts" style="display: flex;flex-direction: column">
    <!--数据添加、修改-->
    <div id="edit" style="display: flex; justify-content: right">
      <el-form :inline="true" :model="listQuery" class="form-inline">
        <el-select v-model="country" filterable placeholder="请选择" size="small" style="width: 150px; margin: 5px">
          <el-option
                  v-for="country in countrys"
                  :key="country"
                  :label="country"
                  :value="country">
          </el-option>
        </el-select>
        <div style="display:inline-block" v-for="(item,index) in editData" :key="index">
          <el-input
                  v-model="listQuery[item.prop]" :placeholder="item.label" clearable size="small" style="width: 150px; margin: 5px"
          />
        </div>
        <el-button type="primary" size="small" @click="saveData" style="margin: 5px">保存</el-button>
        <el-button type="warning" size="small" @click="handleFilterClear" style="margin: 5px">重置</el-button>
        <el-button type="success" size="small" @click="toggleView" style="margin: 5px">{{ isFullscreen ? '收起' : '展开' }}</el-button>
      </el-form>
    </div>

    <el-row id="content">
      <el-col :xs="24" :sm="24" :md="isFullscreen ? 24 : map">
        <!--地图-->
        <div ref="myEchart" id="map-container"></div>
      </el-col>
      <el-col :xs="24" :sm="24" :md="isFullscreen ? 0 : map">
        <div id="right_top" style="margin-bottom: 5px">
          <!--图查询-->
          <div style="margin-bottom:5px">
            <el-button
              style="margin-bottom: 5px"
              :class="{'active': mapValue[index]}"
              v-for="(item,index) in searchOptions"
              :key="item.value"
              @click="toggleSelect(index,item.value)"
            >{{item.label}}</el-button>
          </div>
          <!--表格过滤-->
          <div style="margin-bottom:5px">
            <el-button
              style="margin-bottom: 5px"
              :class="{'active': filterValue[index]}"
              v-for="(item,index) in selectionOptions"
              :key="item.value"
              @click="formFilter(index,item.value)"
            >{{item.label}}</el-button>
          </div>

          <!--api配置-->
          <div class="filter-container">
            <el-form class="form-inline">
              <el-input v-model="url" placeholder="url" clearable size="small" style="width: 400px; margin: 0 10px 10px 0"/>
              <el-input v-model="path" placeholder="保存路径" clearable size="small" style="width: 100px; margin: 0 10px 10px 0"/>
              <el-button type="primary" size="small" @click="addConfig" style="margin-bottom: 5px">保存</el-button>
              <el-select v-model="timeInterval" clearable placeholder="时间间隔" size="small" style="width: 130px; margin-left: 10px">
                <el-option
                  v-for="item in timeOptions"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value">
                </el-option>
              </el-select>
              <el-switch
                v-model="switchValue"
                style="margin:5px 0 5px 10px"
                active-color="#13ce66"
                inactive-color="#ff4949">
              </el-switch>
              <el-button
                v-show="showApi"
                size="small"
                style="margin:0 0 5px 10px"
                :class="{'active': apiActive}"
                @click="showApiBtn"
              >{{searchApi}}</el-button>
            </el-form>
          </div>

          <!--按钮-->
          <div>
            <el-tooltip content="刷新" placement="top">
              <el-button type="warning" icon="el-icon-refresh" circle @click="updatePage" />
            </el-tooltip>
            <el-tooltip content="地图全屏" placement="top">
              <el-button type="success" icon="el-icon-zoom-in" circle @click="toScreenfull" />
            </el-tooltip>
            <el-tooltip content="修改" placement="top">
              <el-button type="primary" icon="el-icon-edit-outline" circle @click="handleUpdate" />
            </el-tooltip>
            <el-select v-model="lang" placeholder="语言" size="small" style="width: 130px; margin: 5px 20px">
              <el-option
                v-for="item in langs"
                :key="item.value"
                :label="item.label"
                :value="item.value">
              </el-option>
            </el-select>
            <el-select v-model="color" placeholder="图颜色" size="small" style="width: 130px; margin: 5px 20px 5px 0">
              <el-option
                v-for="item in colors"
                :key="item.value"
                :label="item.label"
                :value="item.value">
              </el-option>
            </el-select>
            <span>当前时间：{{chinaTime}}</span>
            <span>&nbsp;&nbsp;</span>
            <span v-if="switchValue">更新倒计时：{{remainingTime}}秒</span>
          </div>
        </div>
        <!--表格-->
        <div ref="table">
          <!--表格-->
          <el-table
            :data="tableData"
            height="100%"
            :border="true"
            highlight-current-row
            @row-click="handleEdit"
            @sort-change="sortChange"
          >
            <el-table-column
              :fixed = "item.label === '国家' ? true : false"
              v-for="(item, index) in dynamicColumns"
              :key="index"
              :prop="item.prop"
              :label="item.label"
              :sortable='item.prop'
              width="120"
            >
            </el-table-column>
          </el-table>
        </div>
      </el-col>
    </el-row>

    <cdetailForm ref="fromDetail" @updateData="updateData" />
  </div>
</template>

<script>
  import echarts from "echarts" //引入组件
  import '../../node_modules/echarts/map/js/world.js'
  import axios from 'axios'
  import Qs from 'qs'
  import screenfull from 'screenfull'
  import cdetailForm from './WorldMap/form'

  export default {
    name: "echarts",
    components: {cdetailForm},
    data() {
      return {
        initChartMax: 0,
        initChartText: '',
        chart: null,
        searchOptions: [],
        searchValue: [],
        tableData: [],
        chinaTime: '',
        url: '',
        path: '',
        pin_next: 60,
        pin_enabled: false,
        listQuery: {},
        country: '',
        dynamicColumns: [],
        editData: [],
        countrys: [],
        populations: [],
        markPoint: [],
        newcountryList: [],
        color: 'population',
        colors: [],
        mapValue: [],
        lang: 'zh',
        langs: [],
        filterValue: [],
        selectedValue: [],
        selectionOptions: [],
        currentTime: '',
        switchValue: false,
        timeInterval: 60,
        timeOptions: [
          { label: '1分钟', value: 60 },
          { label: '30分钟', value: 1800 },
          { label: '1小时', value: 3600 },
        ],
        remainingTime: 0,
        pinCounterTimerId: null,
        pinNextTimerId: null,
        desc: '',
        addStatus: 0,
        roam: true,
        mapFilter: [],
        map: 12,
        isFullscreen : false,
        mapData : [],
        searchApi : '',
        apiData : {},
        showApi : false,
        apiActive : false,
      }
    },
    watch: {
      'tableData'(newVal, oldVal) {
        if (this.tableData.length === 215){
          this.mapData = this.tableData;
        }else {
          this.mapData = this.mapData.map(x => {
            if (x.alpha2_code == 'CN'){
              x.current_time = this.chinaTime;
            }
            let found = this.tableData.find(y => x.country === y.country);
            if (found){
              x.current_time = found.current_time;
            }
            return x;
          });
        }
      },
      'country'(newVal, oldVal) {
        if(newVal != oldVal && newVal != ""){
          this.listQuery = this.mapData.find(item => item.country === this.country);

          const filtered = this.mapData.filter(table => table.country === this.country);
          this.markPoint = [{value:this.country,coord:filtered[0].latitude_longitude}];

          this.initChart();
        }
      },
      'lang'(newVal, oldVal) {
        if(newVal != oldVal){
          this.selectedValue = [];
          this.mapFilter = [];

          let url = new URL(window.location.href);
          url.searchParams.set('lang', newVal);
          history.replaceState(null, null, url);

          this.countryList();
        }
      },
      'color'(newVal, oldVal) {
        if(newVal != oldVal){
          this.getColor();
        }
      },
      'switchValue'(newVal, oldVal) {
        if(newVal){
          this.showApi = true;
          !this.pinNextTimerId && !this.pinCounterTimerId && this.startImplement();
        } else {
          this.showApi = false;
          this.pinNextTimerId && clearInterval(this.pinNextTimerId);
          this.pinCounterTimerId && clearInterval(this.pinCounterTimerId);

          this.pinNextTimerId = false;
          this.pinCounterTimerId = false;
          this.markPoint = [];

          if (this.apiActive){
            this.apiActive = false;
            this.selectApi();
          }

          this.initChart();
        }
        this.apiActive = false;
      },
    },
    mounted() {
      this.style();

      const params = new URLSearchParams(window.location.search);
      let lang = params.get('lang');

      if(lang != null){
        this.lang = lang;
        if (lang == 'zh') {
          this.countryList();
        }
      }else {
        let url = new URL(window.location.href);
        url.searchParams.set('lang', 'zh');
        history.replaceState(null, null, url);
        this.countryList();
      }

      this.path = this.$route.params.path;
    },
    methods: {
      showApiBtn() {
        this.apiActive = !this.apiActive;
        this.selectApi();
      },

      selectApi() {
        if (this.apiActive) {
          for (let i = 0; i < this.mapData.length; i++) {
            let country = this.mapData[i]['country'];
            let alpha2_code = this.mapData[i]['alpha2_code'];
            let apiObj = this.apiData[alpha2_code];
            if (!apiObj){
              continue;
            }
            const religion = apiObj[this.searchApi];
            const key = Object.keys(this.countrys).find(key => this.countrys[key] === country);
            if (this.newcountryList[key]) {
              this.newcountryList[key] += '; ' + religion;
            }

            for (let j = 0; j < this.populations.length; j++) {
              const c = this.populations[j].name.split(';')[0];
              if (c === country) {
                this.populations[j].name += "; " + religion;
                break;
              }
            }
          }
        } else {
          for (let i = 0; i < this.mapData.length; i++) {
            let country = this.mapData[i]['country'];
            let alpha2_code = this.mapData[i]['alpha2_code'];
            let apiObj = this.apiData[alpha2_code];
            if (!apiObj){
              continue;
            }
            const religion = apiObj[this.searchApi];
            const key = Object.keys(this.countrys).find(key => this.countrys[key] === country);

            if (this.newcountryList[key]) {
              let religionsArr = this.newcountryList[key].split(';');
              religionsArr = religionsArr.reverse();
              const index = religionsArr.findIndex(rel => rel.trim() === religion);
              if (index !== -1) {
                religionsArr.splice(index, 1);
              }
              religionsArr = religionsArr.reverse();
              this.newcountryList[key] = religionsArr.join(';');
            }

            for (let j = 0; j < this.populations.length; j++) {
              let c = this.populations[j].name.split(';');
              if (c[0] === country) {
                c = c.reverse();
                const index = c.findIndex(rel => rel.trim() === religion);
                if (index !== -1) {
                  c.splice(index, 1);
                }
                c = c.reverse();
                this.populations[j].name = c.join(';');
                break;
              }
            }
          }
        }

        this.initChart();
      },

      style() {
        const editElement = document.querySelector('#edit');
        const editHeight = editElement.offsetHeight;

        const content = window.innerHeight-editHeight;

        const myEchart = this.$refs.myEchart;
        myEchart.style.height = content + 'px';

        const right_top = document.querySelector('#right_top');
        const rightTopHeight = right_top.offsetHeight;

        const table = this.$refs.table;
        table.style.height = (content - rightTopHeight - 150) + 'px';

      },

      toggleView() {
        if (window.innerWidth < 992) {
          return
        }
        this.isFullscreen = !this.isFullscreen;

        if (this.isFullscreen) {
          this.map = 24;
        } else {
          this.map = 12;
        }
        setTimeout(() => {
          const myChart = echarts.init(this.$refs.myEchart);
          myChart.resize();
        }, 50);
      },

      countryList(){
        this.selectionOptions = [];
        this.dynamicColumns = [];
        this.populations = [];
        this.editData = [];
        this.colors = [];
        this.searchOptions = [];
        this.mapValue = [];
        let data = {classify : 'getCountryList',lang: this.lang};
        axios.post('/map.php',Qs.stringify(data),{headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
          .then(res => {
            this.searchValue = res.data.searchValue;
            this.langs = res.data.lang;

            this.newcountryList = res.data.newcountryList;

            this.selectionOptions = res.data.tabelFilter;
            this.filterValue = new Array(this.selectionOptions.length).fill(false);

            this.chinaTime = res.data.chinaTime;
            this.tableData = JSON.parse(res.data.countryList);
            this.tableData = this.tableData.sort(this.descending('population'));

            const d = Object.entries(res.data.dynamicColumns).map(([label,prop]) => ({label,prop}));
            d.forEach(item => {
              this.dynamicColumns.push(item);
            });

            const s = Object.entries(res.data.dynamicColumns).map(([label,value]) => ({label,value}));
            s.forEach(item => {
              this.colors.push(item);
              this.searchOptions.push(item);
              const index = this.searchValue.indexOf(item.value);
              if (index === -1){
                this.mapValue.push(false);
              }else {
                this.mapValue.push(true);
              }
            });
            for (var k of this.searchOptions){
              if (k.value === 'country'){
                this.colors.splice(k,1);
                this.searchOptions.splice(k,1);
                this.mapValue.splice(k,1);
              }
            }

            this.editData = this.editData.concat(this.dynamicColumns);
            for (var k of this.editData){
              if (k.prop === 'country'){
                this.editData.splice(k,1);
              }
            }
            this.countrys = res.data.countrys;

            this.getConfig();

            this.getColor();
          }).catch(err => {
          this.$message.error('数据获取失败！');
        })
      },

      getColor() {
        this.populations = [];
        let data = {classify : 'getColor',color: this.color};
        axios.post('/map.php',Qs.stringify(data),{headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
          .then(res => {
            const newObj = {};
            const obj1 = this.newcountryList;
            const obj2 = res.data.colors;

            for (let key in obj1) {
              if (obj2[key]) {
                newObj[obj1[key]] = obj2[key];
              }else {
                newObj[obj1[key]] = 0;
              }
            }

            const f = Object.entries(newObj).map(([name,value]) => ({name,value}));
            f.forEach(item => {
              this.populations.push(item);
            });

            this.initChartMax = res.data.maxCount + 10000;
            this.initChart();
          }).catch(err => {})
      },

      toggleSelect(i,value) {
        this.$set(this.mapValue, i, !this.mapValue[i]);
        const index = this.searchValue.indexOf(value);
        if (index === -1) {
          this.searchValue.push(value);
          for (let i = 0; i < this.mapData.length; i++) {
            let country = this.mapData[i]['country'];
            let religion = this.mapData[i][value] || '--';

            const key = Object.keys(this.countrys).find(key => this.countrys[key] === country);
            if (this.newcountryList[key]) {
              this.newcountryList[key] += '; ' + religion;
            }

            for (let j = 0; j < this.populations.length; j++) {
              const c = this.populations[j].name.split(';')[0];
              if (c === country) {
                this.populations[j].name += "; " + religion;
                break;
              }
            }
          }
        } else {
          this.searchValue.splice(index, 1);
          for (let i = 0; i < this.mapData.length; i++) {
            let country = this.mapData[i]['country'];
            let religion = this.mapData[i][value] || '--';
            const key = Object.keys(this.countrys).find(key => this.countrys[key] === country);

            if (this.newcountryList[key]) {
              let religionsArr = this.newcountryList[key].split(';');
              religionsArr = religionsArr.reverse();
              const index = religionsArr.findIndex(rel => rel.trim() === religion);
              if (index !== -1) {
                religionsArr.splice(index, 1);
              }
              religionsArr = religionsArr.reverse();
              this.newcountryList[key] = religionsArr.join(';');
            }

            for (let j = 0; j < this.populations.length; j++) {
              let c = this.populations[j].name.split(';');
              if (c[0] === country) {
                c = c.reverse();
                const index = c.findIndex(rel => rel.trim() === religion);
                if (index !== -1) {
                  c.splice(index, 1);
                }
                c = c.reverse();
                this.populations[j].name = c.join(';');
                break;
              }
            }
          }
        }

        this.initChart();
      },

      formFilter(i,value){
        this.$set(this.filterValue, i, !this.filterValue[i]);
        const index = this.selectedValue.indexOf(value);
        if (index === -1) {
          this.selectedValue.push(value);
        } else {
          this.selectedValue.splice(index, 1);
        }

        const that = this;
        let data = {classify : 'getCountryList', filter: this.selectedValue, searchValue: this.searchValue, lang: that.lang};
        axios.post('/map.php',Qs.stringify(data),{headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
          .then(res => {
            that.chinaTime = res.data.chinaTime;
            that.newcountryList = res.data.newcountryList;
            that.tableData = JSON.parse(res.data.countryList);
            that.tableData = that.tableData.sort(that.descending('population'));

            for (let i = 0; i < that.populations.length; i++) {
              let c = that.populations[i].name.split(';');
              const key = Object.keys(that.countrys).find(key => that.countrys[key] === c[0]);

              if (this.newcountryList[key]) {
                that.populations[i].name = that.newcountryList[key];
              }
            }

            that.mapFilter = [];
            if (that.tableData.length != 215 && that.tableData.length != 0){
              for (let i = 0; i < that.populations.length; i++) {
                let nameArr = that.populations[i].name.split(';'); // 将name属性按分号分割成数组
                let name = nameArr[0]; // 取第一个元素
                if (res.data.populations[name]) { // 判断name是否在b对象中存在
                  that.mapFilter.push(i);
                }
              }
            }

            if (that.apiActive){
              that.selectApi();
            }else {
              that.initChart();
            }
          }).catch(err => {})
      },

      updateData() {
        this.updatePage();
      },

      handleUpdate() {
        this.$refs.fromDetail.handleUpdate();
      },

      toScreenfull(){
        if(screenfull.isEnabled){   // 判断是否支持全屏
          screenfull.toggle(this.$refs.myEchart);   // 使用toggle方法
        }else{
          this.$message.error('不支持全屏');
        }
      },

      initChart() {
        if (window.innerWidth < 768) {
          this.roam = {
            zoom: 'scale',
            move: 'pan',
          }
        }else {
          this.roam = true;
        }

        this.chart = echarts.init(this.$refs.myEchart);
        window.onresize = echarts.init(this.$refs.myEchart).resize;
        let that = this;
        let option = {
          backgroundColor: "#02AFDB",
          visualMap: {   //图列显示柱
            type: 'continuous',
            min: 0,
            left:0,
            bottom:20,
            max: that.initChartMax,
            text: [that.initChartText,''],
            realtime: false,
            calculable : true,
            color: ['orangered','yellow','lightskyblue'],
            orient: 'horizontal',// 设置为横向
          },
          toolbox: {  //工具栏
            show: true,
            orient: 'vertical',
            left: 'right',
            top:20,
            itemGap:20,
            left:30,
            feature: {
              dataView: {readOnly: false},
              restore: {},
              saveAsImage: {name:'word'},
            }
          },
          tooltip: {  //提示框组件
            trigger: 'item',
            hideDelay: 800,
            formatter: function (param) {
              return param.name;
            }
          },
          graphic: [{
            type: 'text',
            left: 'center',
            bottom: 200,
            style: {
              text: this.desc,
              fontSize: 18,
            }
          }],
          series: [{
            type: 'map',
            mapType: 'world',
            roam: that.roam,
            mapLocation: {y: 100},
            data: that.populations,
            nameMap: that.newcountryList,
            symbolSize: 12,
            label: {
              normal: {show: false},
              emphasis: {show: false}
            },
            itemStyle: {
              normal: {},
              emphasis: {
                borderColor: '#fff',
                borderWidth: 1,
                areaColor: null,
              }
            },
            markPoint: {
              symbol: 'pin',
              symbolSize: 50,
              data: that.markPoint,
              emphasis: {
                label: {
                  show: true,
                  formatter: function (params) {
                    return params.data.value;
                  },
                },
              },
              itemStyle: {
                color: '#f44336', // 这是一个好看的红色
              },
              label: {
                show: true,
                formatter: function (params) {
                  const regExp = /^(http|https):\/\/[^\s]+$/;
                  let v = regExp.test(params.value);
                  if (v) {
                    const hostname = new URL(params.value).hostname;
                    const subdomain = hostname.split(".")[1];
                    return subdomain.slice(0,5);
                  }
                  return params.value;
                }
              },
            },
          }],
        };

        if (this.mapFilter.length != 0){
          option.series[0].data.forEach((item, index) => {
            if (this.mapFilter.includes(index)) {
              item.itemStyle = {
                normal: {
                  borderColor: '#fff',
                  borderWidth:2,
                  areaColor: null,
                },
              };
            } else {
              item.itemStyle = {
                normal: {},
              };
            }
          });
        }else {
          option.series[0].data.forEach((item, index) => {
            item.itemStyle = {
              normal: {},
            };
          });
        }

        this.chart.setOption(option);

        this.chart.on("click", function(params) {
          if (params.componentType === 'series'){
            that.country = params.name.split(';')[0];
          }else if (params.componentType === 'markPoint') {
            const regExp = /^(http|https):\/\/[^\s]+$/;
            let v = regExp.test(params.value);
            if (v) {
              window.open(params.value);
            }
          }
        });
      },

      addConfig() {
        let data = {classify : 'addConfig', path: this.path, url: this.url, enabled: this.switchValue, next: this.timeInterval };
        if (this.addStatus){
          data.status = 1
        }
        axios.post('/map.php',Qs.stringify(data),{headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
                .then(res => {
                  if (res.data.status === 0){
                    this.$confirm(res.data.msg)
                            .then(_ => {
                              this.addStatus = 1;
                              this.addConfig();
                            })
                            .catch(_ => {})
                  }else {
                    this.$message.success(res.data.msg);
                    this.addStatus = 0;
                    this.$router.replace('/worldmap/'+this.path);
                  }
                }).catch(err => {
          this.addStatus = 0;
        })
      },

      getConfig() {
        let data = {classify : 'getConfig', path: this.path};
        let that = this;
        axios.post('/map.php',Qs.stringify(data),{headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
                .then(res => {
                  if (res.data && res.data.api) {
                    this.url = res.data.api;
                    this.pin_enabled = res.data.enabled;
                    this.pin_next = res.data.next;
                    this.timeInterval = parseInt(this.pin_next);
                  } else {
                    this.url = "/api.php";
                    this.pin_enabled = false;
                    this.pin_next = 60;
                    this.timeInterval = this.pin_next;
                  }

                  if (this.pin_enabled == 1) {
                    this.startImplement();
                  }

                  this.switchValue = this.pin_enabled == 1;


                }).catch(err => {
          console.log(err);
        })
      },

      startImplement() {
        this.remainingTime = this.timeInterval;
        this.getMarkPoint();
        this.pinNextTimerId = setInterval(this.getMarkPoint, this.timeInterval * 1000);
        this.pinCounterTimerId = setInterval(this.countdown, 1000);
      },

      countdown() {
        if (this.remainingTime > 0) {
          this.remainingTime--;
        }
      },

      getMarkPoint() {
        let that = this;
        axios.post(that.url).then(resp => {
          that.remainingTime = that.timeInterval;
          that.desc = resp.data.desc;
          that.markPoint.splice(0);

          that.apiData = resp.data.data;

          const areas = Object.entries(resp.data.data).map(([key, value]) => ({key, value}));
          const keys = Object.keys(areas[0]['value']);
          that.searchApi = keys[1];
          areas.forEach(area => {
            let area_full = that.mapData.find(c => c["alpha2_code"] == area.key);
            if (area_full) {
              that.markPoint.push({"value": area.value.count, "coord": area_full.latitude_longitude});
            } else {
              console.log("cannot find area " + JSON.stringify(area));
            }
          });
          this.getTime();
        }).catch(err => {
          console.log(err);
        })
      },

      getTime() {
        let data = {classify : 'getCountryList',lang: this.lang, searchValue: this.searchValue};
        axios.post('/map.php',Qs.stringify(data),{headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
                .then(res => {
                  this.chinaTime = res.data.chinaTime;
                  this.tableData = JSON.parse(res.data.countryList);
                  this.tableData = this.tableData.sort(this.descending('population'));

                  this.newcountryList = res.data.newcountryList;
                  for (let j = 0; j < this.populations.length; j++) {
                    const zhCountry = this.populations[j].name.split(';')[0];
                    const enCountry = Object.keys(this.countrys).find(key => this.countrys[key] === zhCountry);
                    this.populations[j].name = this.newcountryList[enCountry];
                  }

                  this.selectApi();
                }).catch(err => {
          console.log(err);
        })
      },

      handleEdit(row) {
        this.country = row.country;
        this.markPoint = [{value:row.country,coord:row.latitude_longitude}];

        this.initChart();
      },

      saveData(){
        if (this.listQuery.country === '' || this.listQuery.country === null){
          this.$message.warning("请先选择国家")
        }else {
          let data = {classify : 'saveData', listQuery : this.listQuery, lang: this.lang};
          axios.post('/map.php',Qs.stringify(data),{headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
            .then(res => {
              if (res.data.status === 1){
                this.$message.success(res.data.message);
                this.edit = true;
                this.handleFilterClear();
              }else {
                this.$message.warning(res.data.message);
              }
            }).catch(err => {
              this.$message.error('保存失败！');
              this.handleFilterClear();
          })
        }
      },

      handleFilterClear() {
        this.listQuery = {};
        this.country = '';
      },

      updatePage(){
        window.location.reload(true);
      },

      sortDevName(str1, str2) {
        let res = 0;
        const str1Type = this.getChartType(str1);
        const str2Type = this.getChartType(str2);
        if(str1Type[0] === 'number' && str2Type[0] === 'number'){
          res = str1 - str2;
        }else {
          for (let i = 0; ;i++) {
            if (!str1[i] || !str2[i]) {
              res = str1.length - str2.length;
              break;
            }
            const char1 = str1[i];
            const char1Type = this.getChartType(char1);
            const char2 = str2[i];
            const char2Type = this.getChartType(char2);
            // 类型相同的逐个比较字符
            if (char1Type[0] === char2Type[0]) {
              if (char1 === char2) {
                continue;
              } else {
                if (char1Type[0] === 'zh') {
                  res = char1.localeCompare(char2);
                } else if (char1Type[0] === 'en') {
                  res = char1.charCodeAt(0) - char2.charCodeAt(0);
                } else {
                  res = char1 - char2;
                }
                break;
              }
            } else {
              // 类型不同的，直接用返回的数字相减
              res = char1Type[1] - char2Type[1];
              break;
            }
          }
        }
        return res;
      },

      getChartType(char) {
        if (/^[\u4e00-\u9fa5]*$/.test(char)) {
          return ['zh', 300];
        }
        if (/^[a-zA-Z]*$/.test(char)) {
          return ['en', 200];
        }
        if (/^[0-9]*$/.test(char)) {
          return ['number', 100];
        }
        return ['others', 999];
      },

      sortChange(column) {
        if (column.order === 'ascending'){
          this.tableData = this.tableData.sort(this.ascending(column.prop));
        }else {
          this.tableData = this.tableData.sort(this.descending(column.prop));
        }
      },

      descending(i){
        const that = this;
        return function(a,b) {
          return that.sortDevName(b[i],a[i]);
        }
      },

      ascending(i){
        const that = this;
        return function(a,b) {
          return that.sortDevName(a[i],b[i]);
        }
      },

    }
  }
</script>
<style>
  html,
  body{
    margin: 0;
    padding: 0;
    border: 0;
  }
  .el-button.active {
    background-color: #409EFF;
    color: #fff;
  }
</style>
