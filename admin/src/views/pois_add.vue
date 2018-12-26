<template>
<div id="main">
    <group class="group-title">
        <x-input title="标题" v-model="title" class="sub-text"></x-input>
        <x-input title="所在地" v-model="locate" class="sub-text"></x-input>
        <x-input title="标签(逗号分隔)" v-model="tags" class="sub-text"></x-input>
        <div class="flex-box">
            <div class="sub-text gitem" style="width:30%;">
                <x-input title="距离(km)" v-model="distance" class="sub-text"></x-input>
            </div>
            <div class="sub-text gitem" style="width:30%;">
                <x-input title="海拔(m)" v-model="altitude" class="sub-text"></x-input>
            </div>
            <div class="sub-text gitem" style="width:30%;">
                <x-input title="爬升(m)" v-model="cumulative_up" class="sub-text"></x-input>
            </div>
        </div>
    </group>
    <group class="group-title" title="描述">
        <x-textarea v-model="desc" class="sub-text"></x-textarea>
    </group>
    <group class="group-title" title="攻略">
        <x-textarea v-model="guide" class="sub-text"></x-textarea>
    </group>
    <group class="group-title" title="封面图片">
         <div class="flex-box">
      
            <div class="sub-text gitem" style="width:60%;">
                <div>大图</div>
                <div><input type="file" id="cover" @change="previewCover" accept="image/*"/></div>
                <div><img id="cover_preview" width="250" height="150"/></div>
            </div>
            <div class="sub-text gitem" style="width:35%;">
                <div>缩略图</div>
                <div><input type="file" id="thumb" @change="previewThumb" accept="image/*"/></div>
                <div><img id="thumb_preview" width="150" height="100"/></div>
            </div>
        </div>
    </group>

    <group class="group-title" title="内容图片">
        <div class="flex-box">
            <div  style="width:150px;" class="sub-text gitem" v-for="(item,index) in 9" :key="index">
                <div><input type="file" :id="item" @change="addImgs" accept="image/*"/></div>
                <div><img :id="'imgs_'+item" width="150" height="120"/></div>
            </div>
        </div>
    </group>

    <x-button action-type="button" @click.native="save" class="sub-text"> 保存 </x-button>
</div>
</template>

<script>
import {XTextarea, XInput, Group, XButton, TransferDomDirective as TransferDom, Flexbox, FlexboxItem} from 'vux'
import dao from '../dao'

export default {
  directives: {
    TransferDom
  },
  components: {XTextarea, XInput, Group, XButton, Flexbox, FlexboxItem},
  data () {
    return {
      title: '',
      locate: '',
      tags: '',
      desc: '',
      guide: '',
      distance: 0,
      altitude: 0,
      cumulative_up: 0,
      thumb_img: '',
      cover_img: '',
      imgs: []
    }
  },
  methods: {
    previewCover: function (e) {
      var f = e.target.files[0]
      this.cover_img = f
      var reads = new FileReader()
      reads.onload = function (e) {
        document.getElementById('cover_preview').src = this.result
      }
      reads.readAsDataURL(f)
    },
    previewThumb: function (e) {
      var f = e.target.files[0]
      this.thumb_img = f
      var reads = new FileReader()
      reads.onload = function (e) {
        document.getElementById('thumb_preview').src = this.result
      }
      reads.readAsDataURL(f)
    },
    addImgs: function (e) {
      var id = e.target.id
      var f = e.target.files[0]
      this.imgs[id] = f
      var reads = new FileReader()
      reads.onload = function (e) {
        let target = 'imgs_' + id
        document.getElementById(target).src = this.result
      }
      reads.readAsDataURL(f)
    },
    save: function () {
      if (!this.title || !this.locate || !this.desc || !this.guide) {
        this.$vux.alert.show({
          content: '标题、所在地、描述、攻略 字段均为必填'
        })
      }
      if (!this.cover_img || !this.thumb_img || this.imgs.length < 1) {
        this.$vux.alert.show({
          content: '请选择封面图片及内容图片'
        })
      }

      let data = {
        title: this.title,
        locate: this.locate,
        tags: this.tags,
        desc: this.desc,
        guide: this.guide,
        distance: this.distance,
        altitude: this.altitude,
        cumulative_up: this.cumulative_up,
        thumb_img: this.thumb_img,
        cover_img: this.cover_img,
        imgs: this.imgs
      }
      dao.addPoi(data).then(res => {
        if (!res) return
        this.$vux.toast.show({
          text: '添加成功'
        })
        this.title = this.locate = this.tags = this.desc = this.guide = this.cover_img = this.thumb_img = ''
        this.imgs = []
        this.distance = this.altitude = this.cumulative_up = 0
        document.getElementById('thumb_preview').src = ''
        document.getElementById('thumb').value = ''
        document.getElementById('cover_preview').src = ''
        document.getElementById('cover').value = ''
        for (let i = 1; i <= 9; i++) {
          let tid = 'imgs_' + i
          document.getElementById(i).value = ''
          document.getElementById(tid).src = ''
        }
      })
    }
  },
  created () {
  }
}
</script>

<style scoped>
.flex-box{
    display: -webkit-flex; /* Safari */
    display: flex;
    flex-wrap:  wrap;
 
}
.group-title{
    font-size: 15px;
    margin:10px;
}
.sub-text{
    font-size:14px;
}
.gitem{
    margin:0px;
    padding:5px;
}
</style>
