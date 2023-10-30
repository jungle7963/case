const api = require('../../../api/api')
const utils = require('../../../utils/util')

Page({
    data: {
        loading: !0,
        imgList: [],
        detail: {},
        failed: "",
        audit_datetime: '',
        create_time: '',
        color: [ "rgba(0,0,0,.3)", "#333" ],
        time : '',
        t : '',
        hours : '00'
    },
    onLoad: function(t) {
        this.setData({
            status : wx.getStorageSync('status')
        })
        wx.getStorageSync('status') == 0 ?
        (api.getTaskDetails({code : t.tcode}).then((res) => {
            this.data.t = new Date(res.receive_time).getTime()/1000 + 3600,
            this.setData({
                detail : res.list[0],
            })
            this.start()
        })) : (api.taskImage({id : t.id}).then((res) => {
            let imgList = res.taskImage.image.split('|')
            this.setData({
                detail : res.taskList,
                failed : res.taskImage.failed,
                audit_datetime: res.taskImage.audit_datetime,
                create_time: res.taskImage.create_time,
                imgList : imgList
            })
        }))
    },

    start(){
        var timestamp = Date.parse(new Date());  
        timestamp = timestamp / 1000;
        this.data.t > timestamp ? 
        (this.setData({
            hours: '00',
            time: utils.formatTimeTwo((this.data.t - timestamp),'m:s'),
        }),
        setTimeout(this.start, 1000)
        ) : 
        getCurrentPages()[(getCurrentPages().length - 1)].route == 'pages/task/detail/detail' ?
        (
            this.setData({
                hours: '00',
                time: '00:00'
            }),
            setTimeout(function(){
                wx.navigateBack({
                    delta: -1,
                }) 
            }, 1000)
        ): ''
    },

    process(i) {
        let data = {
            dataset : i.currentTarget.dataset,
          }
          api.taskProcess(data).then((res) => {
            wx.showModal({
                title: "操作提示",
                content: "提交成功",
                success: function(a) {
                    wx.navigateBack({
                        delta: -1,
                    })
                }
            });
        })
    },

    deleteImg: function(e) {
        var t = e.currentTarget.dataset.id, a = this.data.imgList;
        a.splice(t, 1), this.setData({
            imgList: a
        });
    },
    uplaod: function() {
        var e = this;
        wx.chooseImage({
            count: 3,
            sizeType: [ "original", "compressed" ],
            success: function(t) {
                var i = t.tempFilePaths, s = t.tempFilePaths.length, d = 1;
                for (var n in e.setData({
                    loading: !1
                }), i) wx.uploadFile({
                    url: api.upload,
                    header: {
                        'api-token': wx.getStorageSync('token'),
                        'api-name' : 'wxapp'
                    },
                    filePath: i[n],
                    name: "file",
                    success: function(t) {
                        var a = JSON.parse(t.data), i = e.data.imgList;
                        if(a.code == 1){
                            i.push(a.data.url), e.setData({
                                imgList: i
                            }), d == s && e.setData({
                                loading: !0
                            }), d += 1;
                        }
                    }
                });
            }
        });
    },
});