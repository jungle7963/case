<template>
  <el-drawer
          ref="drawer"
          :with-header="false"
          :size="widthSize"
          :before-close="handleClose"
          :visible.sync="dialogFormVisible"
          direction="rtl"
          custom-class="demo-drawer"
  >
    <div class="demo-drawer__content">
      <el-form ref="dataForm" style="width: 100%; padding:10px; height: 100vh;overflow-y: scroll;">
        <el-tabs tab-position="top">
          <el-tab-pane label="基本信息">
            <div>
              <div class="filter-container">
                <el-form class="form-inline">
                  <el-select v-model="filterCol" clearable placeholder="请选择过滤列" size="small" style="width: 200px; margin: 0 20px 15px 0">
                    <el-option
                      v-for="item in header[0].children"
                      :key="item.value"
                      :label="item.label"
                      :value="item.label">
                    </el-option>
                  </el-select>
                  <el-select v-model="filterParam" clearable placeholder="请选择过滤参数" size="small" style="width: 200px; margin: 0 20px 15px 0">
                    <el-option
                      v-for="item in filterParams"
                      :key="item.value"
                      :label="item.label"
                      :value="item.label">
                    </el-option>
                  </el-select>
                  <el-button type="primary" size="small" @click="addFilter" style="margin-bottom: 5px">添加</el-button>
                  <el-button  size="small" @click="filterClear" style="margin-bottom: 5px">重置</el-button>
                </el-form>
              </div>

              <el-tree
                style="width: 60%"
                :data="tableFilter"
                node-key="id"
                :highlight-current='true'
                :expand-on-click-node="false"
                draggable
                :allow-drop="allowDropFilter"
              >
                <div class="custom-tree-node" slot-scope="{ node, data }" style="width: 100%; display: flex; justify-content: space-between">
                  <div>{{ node.label }}</div>
                  <div v-if="node.label !== '表格过滤'" style="margin-left: 100px">
                    <el-button
                      type="text"
                      size="mini"
                      icon="el-icon-close"
                      @click="delFilter(node, data)">
                    </el-button>
                  </div>
                </div>
              </el-tree>
            </div>

            <!--表头-->
            <div style="margin-top: 30px">
              <div class="filter-container">
                <el-form class="form-inline">
                  <el-input v-model="addCol" placeholder="列名" clearable size="small" style="width: 200px; margin: 0 20px 15px 0"/>
                  <el-input v-model="enCol" placeholder="英文列名" clearable size="small" style="width: 200px; margin: 0 20px 15px 0"/>
                  <el-button type="primary" size="small" @click="addColumn" style="margin-bottom: 5px">添加</el-button>
                  <el-button  size="small" @click="columnClear" style="margin-bottom: 5px">重置</el-button>
                </el-form>
              </div>

              <el-tree
                style="width: 60%"
                :data="header"
                node-key="id"
                :highlight-current='true'
                :expand-on-click-node="false"
                draggable
                :allow-drop="allowDropHeader"
              >
                <div class="custom-tree-node" slot-scope="{ node, data }" style="width: 100%; display: flex; justify-content: space-between">
                  <div>{{ node.label }}</div>
                  <div v-if="node.label !== '表头' && node.label !== '国家'" style="margin-left: 100px">
                    <el-button
                      type="text"
                      size="mini"
                      icon="el-icon-close"
                      @click="delFilter(node, data)">
                    </el-button>
                  </div>
                </div>
              </el-tree>
            </div>

            <!--地图鼠标悬浮默认显示-->
            <div style="margin-top: 30px">
              <div class="filter-container">
                <el-form class="form-inline">
                  <el-select v-model="searchVal" clearable placeholder="请选择" size="small" style="width: 200px; margin: 0 20px 15px 0">
                    <el-option
                      v-for="item in header[0].children"
                      :key="item.value"
                      :label="item.label"
                      :value="item.label"
                      :disabled="item.label === '国家'">
                    </el-option>
                  </el-select>
                  <el-button type="primary" size="small" @click="addSearch()" style="margin-bottom: 5px">添加</el-button>
                </el-form>
              </div>

              <el-tree
                style="width: 60%"
                :data="searchValue"
                node-key="id"
                :highlight-current='true'
                :expand-on-click-node="false"
              >
                <div class="custom-tree-node" slot-scope="{ node, data }" style="width: 100%; display: flex; justify-content: space-between">
                  <div>{{ node.label }}</div>
                  <div v-if="node.label !== '地图默认显示'" style="margin-left: 100px">
                    <el-button
                      type="text"
                      size="mini"
                      icon="el-icon-close"
                      @click="delFilter(node, data)">
                    </el-button>
                  </div>
                </div>
              </el-tree>
            </div>
          </el-tab-pane>
        </el-tabs>
      </el-form>
      <div class="demo-drawer__footer" style="position:fixed;top:15px;right:20px;">
        <el-button size="mini" @click="$refs.drawer.closeDrawer()">关 闭</el-button>
        <el-button size="mini" :loading="btnLoading" type="primary" @click="saveData()">保存</el-button>
      </div>
    </div>
  </el-drawer>
</template>

<script>
  import axios from 'axios'
  import Qs from 'qs'
  export default {
    name: 'wordMapForm',
    data() {
      return {
        widthSize: "50%",
        btnLoading: false,
        dialogFormVisible: false,
        tableFilter: [{
          label: '表格过滤',
          children: [],
        }],
        header:[{
          label: '表头',
          children: [],
        }],
        searchValue: [{
          label: '地图默认显示',
          children: [],
        }],
        defaultProps: {
          children: 'children',
          label: 'label'
        },
        filterCol: '',
        filterParam: '',
        filterParams: [],
        addCol: '',
        enCol: '',
        formStatus: false,
        searchVal: '',
      }
    },
    watch: {
      dialogFormVisible: function() {
        this.columnClear();
        this.filterClear();
        this.searchVal = '';
      },
      temp: {
        handler(newVal, oldVal) {},
        immediate: true,
        deep: true
      },
      filterCol: function() {
        this.getFilterParam()
      },
    },

    methods: {
      addSearch() {
        const children = this.searchValue[0].children;

        const isLabel = children.some(item => item.label === this.searchVal);
        if (isLabel) return this.$message.warning("添加的数据已存在");

        const header = this.header[0].children;
        const foundValue = header.find(obj => obj.label === this.searchVal)?.value ?? null;

        this.searchValue[0].children.push({label:this.searchVal,value:foundValue});
        this.$message.success("添加成功");
        this.searchVal = '';
        this.formStatus = true;
      },

      saveData() {
        if (!this.formStatus) return this.handleClose();
        this.btnLoading = true;
        const header = this.header[0].children;
        const tableFilter = this.tableFilter[0].children;
        const search = this.searchValue[0].children;
        let data = {classify : 'saveForm', header: header, tableFilter: tableFilter, search: search};
        axios.post('/map.php',Qs.stringify(data),{headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
          .then(res => {
            if (res.data.status === 1){
              this.$emit('updateData');
              this.dialogFormVisible = false;
              this.$message.success(res.data.msg);
            }else {
              this.$message.error(res.data.msg);
            }
            this.btnLoading = false;
          }).catch(err => {
            this.btnLoading = false;
        })
      },

      getFilterParam() {
        this.filterParams = [];
        let data = {classify : 'getFilterParam', 'filterCol' : this.filterCol};
        const that = this;
        axios.post('/map.php',Qs.stringify(data),{headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
          .then(res => {
            const p = Object.entries(res.data).map(([value,label]) => ({value,label}));
            p.forEach(item => {
              that.filterParams.push(item);
            });
          }).catch(err => {})
      },

      handleUpdate() {
        this.widthSize = window.innerWidth >= 768 ? '50%' : '100%';
        this.header[0].children = [];
        this.tableFilter[0].children = [];
        this.searchValue[0].children = [];
        this.dialogFormVisible = true;
        this.btnLoading = false;
        let data = {classify : 'getcolList'};
        const that = this;
        axios.post('/map.php',Qs.stringify(data),{headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
          .then(res => {
            const c = Object.entries(res.data.colList).map(([label,value]) => ({label,value}));
            c.forEach(item => {
              that.header[0].children.push(item);
            });

            const f = Object.entries(res.data.filter).map(([label,children]) => {
              return {
                label,
                children : Object.values(children)
              };
            });
            f.forEach(item => {
              that.tableFilter[0].children.push(item);
            });

            const s = Object.entries(res.data.searchValue).map(([label,value]) => ({label,value}));
            s.forEach(item => {
              that.searchValue[0].children.push(item);
            });
          }).catch(err => {})
      },

      addColumn(){
        if (this.addCol == "" || this.enCol == "") return this.$message.warning("请输入需添加的列名和英文列名");
        if (this.addCol.length > 21) return this.$message.warning("列名长度需小于22个字符");
        if (this.enCol.length > 10) return this.$message.warning("英文列名长度需小于11个字符");

        const children = this.header[0].children;

        const isLabel = children.some(item => item.label === this.addCol);
        if (isLabel) return this.$message.warning("添加的列名已存在");

        const isValue = children.some(item => item.value === this.enCol);
        if (isValue) return this.$message.warning("添加的英文列名已存在");

        this.header[0].children.push({label:this.addCol,value:this.enCol});
        this.$message.success("添加成功");
        this.columnClear();
        this.formStatus = true;
      },

      allowDropHeader(draggingNode, dropNode, type) {
        if (draggingNode.data.label === '国家'){
          return false;
        }
        if (dropNode.data.label === '国家' && type === 'prev') {
          return false;
        }
        if (type === "inner" || dropNode.level < 2) {
          return false;
        }
        this.formStatus = true;
        return true;
      },

      allowDropFilter(draggingNode, dropNode, type) {
        const sonValue = draggingNode.data.label;
        const arr = this.tableFilter[0].children;
        const result = arr.find(item => item.children.some(child => child.label === sonValue));
        if (result){
          const parentValue = result.label;
          const targetValue = dropNode.parent.data.label;

          if (parentValue !== targetValue){
            return false;
          }
        }
        if (type === "inner") {
          return false;
        }
        this.formStatus = true;
        return true;
      },

      columnClear() {
        this.addCol = '';
        this.enCol = '';
      },

      filterClear() {
        this.filterCol = '';
        this.filterParam = '';
      },

      delFilter(node, data) {
        this.$confirm('此操作将永久删除该数据, 是否删除?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          const parent = node.parent;
          const children = parent.data.children;
          if (data.value){
            const index = children.findIndex(d => d.value === data.value);
            children.splice(index, 1);
          }else {
            const index = children.findIndex(d => d.label === data.label);
            children.splice(index, 1);
          }
          this.formStatus = true;
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消删除'
          })
        })

      },

      addFilter() {
        if (this.filterCol == "" || this.filterParam == "") return this.$message.warning("请选择过滤列和过滤参数");

        const filter = this.tableFilter[0].children;

        const index = filter.findIndex(item => item.label === this.filterCol);

        if (index < 0) {
          const newFilter = {label:this.filterCol,children:[{label:this.filterParam,value:this.filterParam}]};
          this.tableFilter[0].children.push(newFilter);
          this.$message.success("添加成功");
          this.formStatus = true;
          return;
        }

        const isExist = this.tableFilter[0].children[index].children.some(item => item.label === this.filterParam);
        if (isExist) return this.$message.warning("过滤条件已存在");

        this.tableFilter[0].children[index].children.push({label:this.filterParam,value:this.filterParam});
        this.$message.success("添加成功");
        this.formStatus = true;
      },

      handleClose(done) {
        if (!this.formStatus){
          this.dialogFormVisible = false;
          this.formStatus = false;
        }else {
          if (this.btnLoading) {
            return
          }
          this.$confirm('更改将不会被保存，确定要关闭吗？')
            .then(_ => {
              this.dialogFormVisible = false;
              this.formStatus = false;
            })
            .catch(_ => {})
        }
      },
    }
  }
</script>
