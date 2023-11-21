import request from '@/utils/request'

export function release(data) {
  return request({
    url: '/admin/ytvideo/release',
    method: 'post',
    data
  })
}

export function getList(query) {
  return request({
    url: '/admin/ytvideo/index',
    method: 'post',
    data: query
  })
}

export function getListcate(query) {
  return request({
    url: '/admin/ytvideo/index',
    method: 'post',
    data: query
  })
}

export function getListAll() {
  return request({
    url: '/admin/ytvideo/getlists',
    method: 'post'
  })
}

export function getinfo(id) {
  return request({
    url: '/admin/ytvideo/getinfo',
    method: 'post',
    data: { id }
  })
}

export function save(data) {
  return request({
    url: '/admin/ytvideo/save',
    method: 'post',
    data
  })
}

export function del(id) {
  return request({
    url: '/admin/ytvideo/del',
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
    url: '/admin/ytvideo/change',
    method: 'post',
    data
  })
}

export function delAll(data) {
  return request({
    url: '/admin/ytvideo/delall',
    method: 'post',
    data
  })
}

export function changeAll(data) {
  return request({
    url: '/admin/ytvideo/changeall',
    method: 'post',
    data
  })
}
