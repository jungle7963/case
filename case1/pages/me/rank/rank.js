const api = require('../../../api/api')

Page({
    data: {
        rankList: []
    },

    onShow() {
        api.rank({}).then((res) => {
            this.setData({
                rankList : res
            })
          })
    },
})