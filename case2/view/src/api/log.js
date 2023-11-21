import request from '@/utils/request'

export function getList(query) {
  return request({
    url: '/admin/log/index',
    method: 'post',
    data: query
  })
}

export function getOperateList(query) {
  return request({
    url: '/admin/operatelog/index',
    method: 'post',
    data: query
  })
}

export function getUpdateList(query) {
  return request({
    url: '/admin/updatelog/index',
    method: 'post',
    data: query
  })
}

