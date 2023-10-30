const api = require('../../../api/api')

Page({
    data: {
        headimg : '',
        detail: []
    },
    onShow: function() {
        let data = {}
        api.me(data).then((res) => {
            this.setData({
                headimg : res.headimg,
                detail : res,
            })
        })
    },

    uplaod: function() {
        var t = this;
        wx.chooseImage({
            count: 3,
            sizeType: [ "original", "compressed" ],
            success: function(e) {
                var i = e.tempFilePaths;
                e.tempFilePaths.length;
                t.setData({
                    loading: !1
                }), 
                wx.uploadFile({
                    url: api.upload,
                    header: {
                        'api-token': wx.getStorageSync('token'),
                        'api-name' : 'wxapp'
                      },
                    filePath: i[0],
                    name: "file",
                    success: function(res) {
                        var e = JSON.parse(res.data);
                        if(e.code == 1){
                            t.setData({
                                headimg: e.data.url,
                            });
                        }
                    }
                });
            }
        });
    },

    formSubmit(t) {
        t.detail.value.nickname == this.data.detail.nickname && t.detail.value.username == this.data.detail.username && t.detail.value.phone == this.data.detail.phone && this.data.detail.headimg == this.data.headimg ? wx.showToast({
            title: '信息没有修改',
            icon: 'none'
        }) : 
        this.data.headimg == '' ? wx.showToast({
            title: '请先上传头像',
            icon: 'none'
        }) :
        t.detail.value.nickname == '微信用户' ? wx.showToast({
            title: '用户名不能为微信用户',
            icon: 'none'
        }) :
        t.detail.value.nickname.length > 6 ? wx.showToast({
          title: '用户名长度不得大于6位',
          icon: 'none'
        }) : t.detail.value.username.length > 6 ? wx.showToast({
            title: '姓名长度不得大于6位',
            icon: 'none'
          }) : !/^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/.test(t.detail.value.phone) ? wx.showToast({
            title: '请输入有效的手机号码',
            icon: 'none'
          }) :
        api.saveUser(
            {
                nickname: t.detail.value.nickname,
                username: t.detail.value.username,
                phone: t.detail.value.phone,
                headimg: this.data.headimg,
            }
        ).then((res) => {
              wx.showModal({
                title: "操作提示",
                content: "保存成功",
                success: function(a) {
                    a.confirm && wx.navigateBack({
                        delta: 1
                    });
                }
            });
        })
    },
});