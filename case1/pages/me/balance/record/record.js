const api = require('../../../../api/api')

Page({
    data: {
        list: []
    },
    
    onShow() {
      api.withDrawalList().then((res) => {
        this.setData({
            list : Object.values(res)
        })
      })
    },

    
})