const api = require('../../../api/api')

Page({
    data: {
      balance : 0
    },
    
    onShow() {
      api.me().then((res) => {
        this.setData({
            balance : res.balance
        })
    })
    },

    toRecord() {
        wx.navigateTo({
            url: "/pages/me/balance/record/record"
        });
    },

    showModal: function(e) {
        this.setData({
            modalName: e.currentTarget.dataset.target
        });
    },

    hideModal: function(e) {
        this.setData({
            modalName: null
        });
    },

    formSubmit(t) {
      const that = this  
      let amount = t.detail.value.amount
      10<=amount && amount<=that.data.balance ? 
      (api.withDrawal({amount:amount}).then((res) => {
            wx.showToast({
              title: '提交成功，待审核',
              icon:'none'
            })
            this.setData({
              modalName: null,
              balance: that.data.balance - t.detail.value.amount
            })
          }
      )) : wx.showToast({
        title: "最低提现10元，最高不超过余额",
        icon:'none'
      })
  },
})