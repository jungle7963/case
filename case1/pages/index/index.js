const api = require('../../api/api')
const utils = require('../../utils/util')

Page({
  data: {
    isShow: !1,
    scrollLeft: 0,
    taskList: [],
    page: 1,
    status: '',
    is_last: !1,
    imgUrls: [],
    indicatorDots: false,  //是否显示面板指示点
    autoplay: true,      //是否自动切换
    interval: 3000,       //自动切换时间间隔
    duration: 1000,       //滑动动画时长
    swiperNav:{
    　　i:0,
    　　arr:[]
    }
  },
  
  onShow() {
    wx.getStorageSync('token') != '' ?
    api.getBanner({keys : 12345}).then((res) => {
        this.setData({
            imgUrls : res
        })
    }) :
    api.banner({keys : 12345}).then((res) => {
      this.setData({
          imgUrls : res
      })
    })

    api.taskType().then((res) => {
      this.setData({
          'swiperNav.arr' : res
      })
      wx.getStorageSync('token') != '' ?
      api.getTasks({
        typeId : this.data.swiperNav.i == 0 ? this.data.swiperNav.arr[0]['id'] : wx.getStorageSync('typeId') != '' ? wx.getStorageSync('typeId') : this.data.swiperNav.arr[0]['id']
      }).then((res) => {
        this.setData({
            taskList : res,
            status : 1
        })
      }) :
      api.taskList({
        typeId : this.data.swiperNav.i == 0 ? this.data.swiperNav.arr[0]['id'] : wx.getStorageSync('typeId') != '' ? wx.getStorageSync('typeId') : this.data.swiperNav.arr[0]['id']
      }).then((res) => {
        this.setData({
            taskList : res,
            status : 0
        })
      })
    })
  },

  onPageScroll:function(e){
    this.setData({
        scrollTop: e.scrollTop
    })
  },

  toDetail: function(e) {
      utils.util.checkToken() ?
        (e.currentTarget.dataset.status == 1 ? 
            wx.navigateTo({
                url: "/pages/index/taskDetail/taskDetail?index_task=1&id=" + e.currentTarget.dataset.id + "&code=" + e.currentTarget.dataset.code
            }) :
            wx.navigateTo({
                url: "/pages/index/taskDetail/taskDetail?code=" + e.currentTarget.dataset.code
            }))
        : wx.showModal({
            title: "操作提示",
            content: "请先登录",
            success: function(a) {
                a.confirm ? '' : wx.navigateBack({
                    delta: 1
                });
            }
        });
  },

  tabSelect: function(t) {
    var w=wx.getSystemInfoSync().windowWidth;
    　var leng=this.data.swiperNav.arr.length;
    　var i = t.target.dataset.i;
    　var disX = (i - 2) * w / leng;
    　if(i!=this.data.swiperNav.i){
    　　this.setData({
    　　　'swiperNav.i':i
    　　})
    　}
    　this.setData({
    　　'swiperNav.x':disX
    　})

    var that = this;
    let data = {
        typeId : t.currentTarget.dataset.type
    }
    wx.setStorageSync('typeId', t.currentTarget.dataset.type)
    wx.getStorageSync('token') != '' ?
    api.getTasks(data).then((res) => {
        that.setData({
            taskList : res,
            status : 1
        })
    }) :
      api.taskList(data).then((res) => {
        that.setData({
            taskList : res,
            status : 0
        })
    })
},
})
