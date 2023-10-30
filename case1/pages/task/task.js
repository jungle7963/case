const api = require('../../api/api')
const utils = require('../../utils/util')

Page({
    data: {
        isShow: !1,
        TabCur: 0,
        list: [],
    },

    onShow: function() {
        this.setData({
            TabCur: wx.getStorageSync('status'),
        })
        let data = {
            status : wx.getStorageSync('status'),
        }
          api.taskStatusList(data).then((res) => {
                this.setData({
                    list : Object.values(res)
                })
        })
    },

    cancel(t) {
        const that = this
        let data = {
            code : t.currentTarget.dataset.code,
            status : wx.getStorageSync('status')
          }
          wx.showModal({
            title: "操作提示",
            content: "任务未完成，是否取消",
            success: function(a) {
                a.confirm ?
                api.taskCancel(data).then((res) => {
                    that.setData({
                        list : Object.values(res),
                    })
                }) : ''
            }
        });
    },

    del(t) {
        const that = this
        let data = {
            code : t.currentTarget.dataset.code,
            status : wx.getStorageSync('status')
          }
          wx.showModal({
            title: "操作提示",
            content: "是否删除该任务",
            success: function(a) {
                a.confirm ?
                api.taskCancel(data).then((res) => {
                    that.setData({
                        list : Object.values(res),
                    })
                }) : ''
            }
        });
    },

    tabSelect: function(t) {
        wx.setStorageSync('status',t.currentTarget.dataset.id)
        this.setData({
            TabCur: wx.getStorageSync('status'),
        })
        let data = {
            status : wx.getStorageSync('status'),
        }
          api.taskStatusList(data).then((res) => {
            this.setData({
                list : Object.values(res)
            })
        })
    },

    viewOrder: function(t) {
        utils.util.checkToken() ?
        (wx.navigateTo({
            url: "/pages/task/detail/detail?tcode=" + t.target.dataset.code + "&id=" + t.target.dataset.id
        })) : ""
    },
})