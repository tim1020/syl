<template>
<div id="main">
    <div class="box" style="text-align:right;">共 {{total}} 条, {{pages}} 页, 当前第 {{cpage}} 页</div>
    <x-table class="box">
        <thead>
          <tr>
            <th>线路名称</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item,index) in pois" :key="index">
            <td>{{item.title}}</td>
            <td><x-button :id="item.id" @click.native="edit" class="nav-btn">编辑</x-button></td>
          </tr>
        </tbody>
    </x-table>
    <div class="box nav">
        <div v-if="cpage!=1"><x-button @click.native="first" class="nav-btn">首页</x-button></div>
        <div v-if="cpage>1"><x-button @click.native="pre" class="nav-btn">上页</x-button></div>
        <div v-if="cpage<pages"><x-button @click.native="next" class="nav-btn">下页</x-button></div>
        <div v-if="cpage!=pages"><x-button @click.native="last" class="nav-btn">尾页</x-button></div>
    </div>

</div>
</template>

<script>
import {Group, XButton, XTable, TransferDomDirective as TransferDom} from 'vux'
import dao from '../dao'

export default {
  directives: {
    TransferDom
  },
  components: {Group, XTable, XButton},
  data () {
    return {
      total: 0,
      pages: 0,
      cpage: 1,
      pois: []
    }
  },
  methods: {
    load: function () {
      dao.lstPoi(this.cpage).then(res => {
        this.total = res.total
        this.pages = res.pages
        if (res.list) {
          this.pois = res.list
        }
      })
    },
    edit: function (e) {
      // let id = e.target.id
    },
    first: function () {
      this.cpage = 1
      this.load()
    },
    last: function () {
      this.cpage = this.pages
      this.load()
    },
    pre: function () {
      this.cpage --
      this.load()
    },
    next: function () {
      this.cpage ++
      this.load()
    }
  },
  created () {
    this.load()
  }
}
</script>

<style scoped>
.box{
    font-size:14px;
    margin:0px 20px;
    padding:15px;
}
.nav-btn{
    font-size:14px;
}
.nav{
    display:flex;
    display:-webkit-flex;
}
.nav div{
    margin:5px;
}
</style>