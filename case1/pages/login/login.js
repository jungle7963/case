const api = require('../../api/api')

Page({
    data: {
      userInfo: {},
      hasUserInfo: false,
      canIUseGetUserProfile: false,
    },
    onLoad() {
      if (wx.getUserProfile) {
        this.setData({
            canIUseGetUserProfile: true
        })
      }
    },
    cancel() {
        wx.navigateBack({
            delta: -1,
        })
    },
    getUserProfile() {
        wx.getUserProfile({
          desc: '用于完善会员资料', 
          success: (res) => {
            wx.login({
                success (res) {
                  if (res.code) {
                    let data = {
                        code: res.code,
                    }
                    api.login(data).then((res) => {
                      wx.setStorageSync('token', res.token.token)
                      wx.navigateBack({
                          delta: -1,
                      })
                    })
                  } else {
                    console.log('登录失败！' + res.errMsg)
                  }
                }
              })
          }
        })
      },
   
})
  