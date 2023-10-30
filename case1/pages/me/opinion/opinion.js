const api = require('../../../api/api')

Page({
    data: {
        currentWord: 0,
    },

    onShow() {
        this.setData({
            currentWord: 0
        })
    },

    limitWord: function (e) {
        var that = this;
        var value = e.detail.value;
        var wordLength = parseInt(value.length); //解析字符串长度转换成整数。
        if (200 < wordLength) {
          return;
        }
        that.setData({
          currentWord: wordLength
        });
      },

    bindFormSubmit(e){
        e.detail.value.textarea.length < 1 ? wx.showToast({
          title: '你还没有输入',
          icon: 'none'
        }) :
        api.opinion({opinion : e.detail.value.textarea}).then((res) => {
            wx.showToast({
              title: '提交成功',
              icon: 'none'
            })
            setTimeout(function(){
                wx.navigateBack({
                    delta: -1,
                }) 
            }, 1000)
        })
    },
})