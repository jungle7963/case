import request from '@/utils/request'

export function getList(query) {
  return request({
    url: '/admin/channel/index',
    method: 'post',
    data: query
  })
}

export function getListcate(query) {
  return request({
    url: '/admin/channel/index',
    method: 'post',
    data: query
  })
}

export function getListAll() {
  return request({
    url: '/admin/channel/getlists',
    method: 'post'
  })
}

export function getinfo(id) {
  return request({
    url: '/admin/channel/getinfo',
    method: 'post',
    data: { id }
  })
}

export function save(data) {
  return request({
    url: '/admin/channel/save',
    method: 'post',
    data
  })
}

export function del(id) {
  return request({
    url: '/admin/channel/del',
    method: 'post',
    data: { id }
  })
}

export function change(id, field, value) {
  const data = {
    val: id,
    field: field,
    value: value
  }
  return request({
    url: '/admin/channel/change',
    method: 'post',
    data
  })
}

export function delAll(data) {
  return request({
    url: '/admin/channel/delall',
    method: 'post',
    data
  })
}

export function changeAll(data) {
  return request({
    url: '/admin/channel/changeall',
    method: 'post',
    data
  })
}
