<template>
    <div id="main">
    <group class="group-title" >
        <div style="display:flex;">
            <x-input title="添加热词" v-model="hotword" class="sub-text"></x-input>
            <x-button style="width:180px;"  action-type="button" @click.native="add_hotword" class="sub-text">确定</x-button>
        </div>
    </group>
    <group class="group-title">
        <div style="display:flex;">
            <x-input title="添加标签" v-model="tag" class="sub-text"></x-input>
            <x-button style="width:180px;"  action-type="button" @click.native="add_tag" class="sub-text">确定</x-button>
        </div>
    </group>
    <group class="group-title" title="热词列表">
        <div class="flex-box sub-text">
            <div v-for="(item,index) in hotwords" :key="index" class="item">
                {{item.val}}
                <span @click="delHotword" :id="item.id">X</span>
            </div>
        </div>
    </group>

    <group class="group-title" title="标签列表">
        <div class="flex-box sub-text">
            <div v-for="(item,index) in tags" :key="index" class="item">
                {{item.val}}
                <span @click="delTag" :id="item.id">X</span>
            </div>
        </div>
    </group>

</div>
</template>

<script>
import {XTextarea, XInput, Group, XButton, TransferDomDirective as TransferDom} from 'vux'
import dao from '../dao'

export default {
  directives: {
    TransferDom
  },
  components: {XTextarea, XInput, Group, XButton},
  data () {
    return {
      hotwords: [],
      hotword: '',
      tags: [],
      tag: ''
    }
  },
  methods: {
    init () {
      this.reload('tags')
      this.reload('hotwords')
    },
    delHotword: function (e) {
      let id = e.target.id
      dao.delHotword(id).then(res => {
        this.reload('hotwords')
      })
    },
    delTag: function (e) {
      let id = e.target.id
      let _this = this
      this.$vux.confirm.show({
        content: '确定要删除该内容？',
        onConfirm () {
          dao.delTag(id).then(res => {
            _this.reload('tags')
          })
        }
      })
    },
    // 保存
    add_tag: function () {
      if (!this.tag) return
      dao.addTag(this.tag).then(res => {
        this.reload('tags')
      })
    },
    add_hotword: function () {
      if (!this.hotword) return
      dao.addHotword(this.hotword).then(res => {
        this.reload('hotwords')
      })
    },
    reload: function (type) {
      if (type === 'tags') {
        dao.getTag().then(res => {
          this.tags = res
        })
      } else if (type === 'hotwords') {
        dao.getHotword().then(res => {
          this.hotwords = res
        })
      }
    }
  },
  created () {
    this.init()
  }
}
</script>

<style scoped>
.group-title {
    font-size:15px;
    margin:30px;
}
.sub-text{
    font-size: 14px;
}
.flex-box{
    display: -webkit-flex; /* Safari */
    display: flex;
    flex-wrap:  wrap;
    padding:10px;
}
.item{
    border:1px solid #c05050;
    padding:3px 8px;
    margin: 3px 8px;
}
</style>