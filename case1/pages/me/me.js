const api = require('../../api/api')
const utils = require('../../utils/util')

Page({
  data: {
    userInfo: {},
  },

    onShow: function() {
        this.setData({
            userInfo : ''
        })
      let data = {}
      api.me(data).then((res) => {
        this.setData({
            userInfo : res
        })
      })
    },
  // 登录
  toLogin: function() {
    wx.navigateTo({
        url: "/pages/login/login"
    });
  },

  toHome: function() {
    wx.switchTab({
      url: "/pages/index/index"
    });
  },

  toEdit: function() {
    wx.navigateTo({
      url: "/pages/me/edit/edit"
    });
  },

  toBalance: function() {
    utils.util.checkToken() ?
    wx.navigateTo({
        url: "/pages/me/balance/balance"
      }) : '' 
  },

  toRank: function() {
    utils.util.checkToken() ?
    wx.navigateTo({
      url: "/pages/me/rank/rank"
    }) : ''
  },

  toHelp: function() {
    wx.navigateTo({
      url: "/pages/me/help/help"
    });
  },

  toCustomer: function() {
    utils.util.checkToken() ?
    wx.navigateTo({
      url: "/pages/me/opinion/opinion"
    }) : ''
  },

  toAbout: function() {
    wx.navigateTo({
      url: "/pages/me/about/about"
    });
  },

  toTask: function(e) {
    utils.util.checkToken() ?
    (wx.setStorageSync('status',e.currentTarget.dataset.id),
    wx.navigateTo({
      url: "/pages/task/task"
    })) : ''
  },

})
