<template>
<div>
    <group class="title" title="请登录">
      <x-input title="帐号" v-model="user" class="sub-text"></x-input>
      <x-input title="密码" v-model="pass" class="sub-text"></x-input>
      <x-button type="primary" action-type="button" @click.native="signin" class="sub-text"> 提交 </x-button>
    </group>
</div>
</template>


<script>
import {XInput, Group, XButton} from 'vux'
import dao from '../dao'

export default {
  components: {
    XInput, Group, XButton
  },
  data () {
    return {
      user: '',
      pass: ''
    }
  },
  methods: {
    signin: function () {
      dao.signin(this.user, this.pass).then(res => {
        localStorage.setItem('token', res.token)
        this.$router.push('tags')
      })
    }
  }
}
</script>

<style scoped>
.title{
    font-size:15px;margin:30px;
}
.sub-text{
    font-size:14px;
}
</style>
