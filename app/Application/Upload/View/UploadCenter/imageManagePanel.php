<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <style>
        .btn-block{
            height: 60px;
            text-align: center;
            line-height: 60px;
            background-color: #fff;
            border-bottom: 1px solid #ececec ;
            border-right: 1px solid #ececec ;
        }
        .btn-block1{
            background-color: #e8e6e6;
        }
        .btn-block:hover{
            background-color: #e8e6e6;
        }
        .aside-bottom{
            position: absolute;
            bottom:10px;
        }
    </style>


    <div id="app" style="" v-cloak>
        <div>
            <el-container style="height: 450px; border: 1px solid #eee;">
                <el-aside width="350px" style="background-color: #fff;border-right:1px solid #eee;height: 100%;overflow: hidden;position: relative">
                    <div style="overflow: auto;height: 380px;border-bottom: 1px solid #eee">
                        <template v-for="(item,index) in cate_list">
                            <div  class="btn-block" v-bind:class="{ 'btn-block1': selectedCate == index }" @click="selectCate(index,item)">
                                {{item.cate_name}}
                            </div>
                        </template>
                    </div>
                    <div class="aside-bottom" style="position: absolute;margin-left: 40px;line-height: 45px;">
                        <el-button type="success" size="small" @click="addCate">新增分类</el-button>

                        <el-button type="primary" size="small" @click="editCate">编辑分类</el-button>
                        <el-button type="danger" size="small" @click="delCate">删除分类</el-button>
                    </div>
                </el-aside>



                <el-container style="flex-wrap：wrap;!important;position: relative">
                    <el-header style="margin-top: 10px;">
                        <el-button type="success" @click="moveToGroup">移动至其他分组</el-button>
                        <el-button @click="upload" type="primary">上传图片</el-button>
                    </el-header>
                    <el-container style="padding: 20px;display:-webkit-box;!important;">
                        <template v-for="(img,index) in galleryList">
                            <div :key="index"
                                 class="imgListItme">
                                <img :src="img.url"
                                     style="width:80px;height: 80px;"
                                     alt="img.name"
                                     @click="selectImgEvent(index)">
                                <div v-if="type == 1 ? isSelect ==  img.url: img.is_select " class="is_check" @click="selectImgEvent(index)">
                                    <span style="line-height: 80px;" class="el-icon-check"></span>
                                </div>
                            </div>
                        </template>
                    </el-container>


                    <el-footer>
                        <el-pagination
                            :page-size="pagination.limit"
                            :current-page.sync="pagination.page"
                            :total="pagination.total_items"
                            background
                            layout="prev, pager, next"
                            @current-change="getGalleryList"
                        ></el-pagination>

                    </el-footer>
                    <div class="footer" style="padding-left:20px;background-color: #fff;margin-top: 10px;height: 66px;border-top:#eee;line-height: 66px; ">
                        <el-checkbox v-model="check_all" @change="changeCheck" v-show="start_move">全选</el-checkbox>
                        <el-button type="success" @click="moveGroup" style="width: 120px;">移动分组</el-button>
                        <el-button type="primary" @click="confirm">确定</el-button>
                        <el-button type="default" @click="closePanel">取消</el-button>
                    </div>
                </el-container>
            </el-container>

        </div>


    </div>

    <style>
        /* 页面架构 */
        body {
            margin: 0;
        }

        .footer {
            margin-top: 10px;
            margin-right: 16px;
            float: right;
        }

        }
        /* 上传图片    */
        .thumb-uploader .el-upload {
            border: 1px dashed #d9d9d9;
            border-radius: 6px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .thumb-uploader .el-upload:hover {
            border-color: #409EFF;
        }

        .el-upload__input {
            display: none !important;
        }

        /* 上传图片    */

        /*图库*/
        .imgListItme {
            width: 82px;
            height: 82px;
            border: 1px dashed #d9d9d9;
            border-radius: 6px;
            display: inline-flex;
            margin-right: 10px;
            margin-bottom: 10px;
            position: relative;
            cursor: pointer;
            vertical-align: top;
        }

        .is_check {
            position: absolute;
            top: 0;
            left: 0;
            width: 80px;
            height: 80px;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.6);
            color: #fff;
            font-size: 40px;
        }

        /*图库*/
    </style>

    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                data: {
                    isIndeterminate:true,
                    checkAll: false,
                    checkedCities: ['上海', '北京'],
                    type:2,
                    isSelect:'',
                    activeName: 'uploadLocal',
                    uploadConfig: {
                        uploadUrl: "{:U('Upload/UploadAdminApi/uploadImage')}",
                        max_upload: 6,//同时上传文件数
                        accept: 'image/*' //接收的文件类型，请看：https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-accept
                    },
                    uploadedLocalFileList: [], //本地上传的文件
                    pagination: {
                        page: 1,
                        limit: 25,
                        total_pages: 0,
                        total_items: 0,
                    },
                    galleryList: [], //图库
                    form: {
                        search_date: [],
                        uid: '',
                        ip: '',
                        start_time: '',
                        end_time: '',
                        status: '',
                        sort_time: '',//排序：时间
                    },
                    orgin_type:0,
                    check_all:false,
                    selectedCate:'',
                    cate_list:[],
                    cate_id:'',
                    start_move:false
                },
                watch: {},

                filters: {
                    formatTime(timestamp) {
                        var date = new Date();
                        date.setTime(parseInt(timestamp) * 1000);
                        return moment(date).format('YYYY-MM-DD HH:mm:ss')
                    }
                },
                methods: {
                    moveGroup: function () {
                        this.start_move = true;
                        this.type = 2;
                    },
                    moveToGroup:function(){
                        var that = this;
                        var arr = [];
                        that.galleryList.forEach(function (item,index) {
                            if(item.is_select){
                                arr.push(item);
                            }
                        });
                        if(arr.length == 0){
                            layer.msg('请选择要移动的图片');return;
                        }
                        layer.open({
                            type: 2,
                            title: '移动至其它分组',
                            content: "{:U('Upload/UploadAdminApi/selectGroup')}",
                            area: ['30%', '40%'],
                        })
                    },
                    selectCate: function (index,item) {
                        var that = this;
                        that.selectedCate = index;
                        that.cate_id = item.id;
                        that.getGalleryList();
                    },
                    changeCheck: function(){
                        var that = this;
                        console.log(that.check_all);

                        that.galleryList.forEach(function(item,index){
                            item.is_select = that.check_all;
                        })
                    },
                    handleRemove: function () {

                    },

                    handleUploadSuccess: function (response, file, fileList) {
                        console.log(response)
                        if (response.status) {
                            this.uploadedLocalFileList.push({
                                name: response.data.name,
                                url: response.data.url,
                            })
                        }
                    },
                    handleUploadError: function () {
                        ELEMENT.Message.error('上传失败');
                    },
                    moveToOrderGroups: function (event) {
                        var that = this;
                        cate_id = event.detail.files;
                        var arr = [];
                        that.galleryList.forEach(function (item,index) {
                            if(item.is_select){
                                arr.push(item);
                            }
                        });
                        $.ajax({
                            url: "{:U('Upload/UploadAdminApi/moveImgsToGroup')}",
                            data: {cate_id:cate_id,arr:arr},
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                that.getGalleryList();
                                that.type = that.orgin_type;
                                that.start_move= false;
                            }
                        })
                    },
                    handleExceed: function(){
                        ELEMENT.Message.error('上传文件数量超限制');
                    },
                    getGalleryList: function () {
                        var that = this;
                        var where = {
                            page: that.pagination.page,
                            limit: that.pagination.limit,
                            cate_id:that.cate_id
                        };
                        $.ajax({
                            url: "{:U('Upload/UploadAdminApi/getUploadImageManageList')}",
                            data: where,
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                console.log(res)
                                var data = res.data;
                                that.pagination.page = data.page;
                                that.pagination.limit = data.limit;
                                that.pagination.total_pages = data.total_pages;
                                that.pagination.total_items = data.total_items;
                                var list = [];
                                data.items.map(function (item) {
                                    item.is_select = false;
                                    list.push(item);
                                })
                                that.galleryList = list
                            }
                        })
                    },
                    upload:function(){
                        var that = this;
                        layer.open({
                            type: 2,
                            title: '上传图片',
                            content: "{:U('Upload/UploadAdminApi/uploadImageToCate', ['max_upload' => 9])}",
                            area: ['90%', '80%'],
                            end:function(){
                                that.getGalleryList()
                            }
                        })
                    },
                    selectImgEvent: function (index) {
                        console.log(this.isSelect)
                        if(this.type == 1){
                            this.isSelect = this.galleryList[index].url;
                        }else{
                            this.galleryList[index].is_select = !this.galleryList[index].is_select
                        }
                    },
                    confirm: function(){
                        var that= this;
                        if(that.type == 1){
                            files = that.isSelect;

                        }else{
                            var is_select_list = [];
                            for(item in that.galleryList){
                                if(that.galleryList[item]['is_select']){
                                    is_select_list.push(that.galleryList[item]);
                                }
                            }
                            files = is_select_list;
                        }
                        event = new CustomEvent('ZTBCMS_UPLOAD_FILE', {
                            detail: {
                                files: files,
                                type: that.type,
                                itemIndex : that.itemIndex,
                                content:that.content,
                            }
                        })
                        window.parent.dispatchEvent(event)
                        this.closePanel();
                    },
                    closePanel: function(){
                        if(parent.window.layer){
                            parent.window.layer.closeAll();

                        }else{
                            window.close();
                        }
                    },
                    addCate: function () {
                        var that = this;
                        layer.open({
                            type: 2,
                            title: '添加分组',
                            content: "{:U('Upload/UploadAdminApi/addCate')}",
                            area: ['30%', '40%'],
                            end: function (res) {
                                that.getCates();
                            }
                        })
                    },
                    editCate: function () {
                        var that = this;
                        console.log(that.cate_id);
                        layer.open({
                            type: 2,
                            title: ' 编辑分组',
                            content: "{:U('Upload/UploadAdminApi/addCate')}&cate_id="+that.cate_id,
                            area: ['30%', '40%'],
                            end: function (res) {
                                that.getCates();
                            }
                        })
                    },
                    delCate: function () {
                        var that = this;
                        layer.confirm('确认删除吗？',{btn:['确认','取消']}, function () {
                            $.ajax({
                                url: "{:U('Upload/UploadAdminApi/delCate')}",
                                data: {cate_id:that.cate_id },
                                dataType: 'json',
                                type: 'post',
                                success: function (res) {
                                    if(res.status){
                                        that.getCates();
                                        layer.closeAll();
                                    }else{
                                        layer.msg(res.msg)
                                    }
                                }
                            })
                        })

                    },
                    getCates: function () {
                        var that = this;
                        $.ajax({
                            url: "{:U('Upload/UploadAdminApi/getCates')}",
                            data: {},
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                console.log(res)
                                var data = res.data;
                                that.cate_list = data.items.cate_list;
                                that.cate_id = data.items.cate_list[0]['id'];
                                that.pagination.page = data.page;
                                that.pagination.limit = data.limit;
                                that.pagination.total_pages = data.total_pages;
                                that.pagination.total_items = data.total_items;
                                var list = [];
                                data.items.img_list.map(function (item) {
                                    item.is_select = false;
                                    list.push(item);
                                })
                                that.galleryList = list
                            }
                        })

                    }

                },
                mounted: function () {

                    this.getCates();
                    window.addEventListener('MOVE_GROUP', this.moveToOrderGroups.bind(this));
                    this.uploadConfig.max_upload = parseInt(this.getUrlQuery('max_upload') || this.uploadConfig.max_upload);
                    this.type = parseInt(this.getUrlQuery('type'));
                    this.orgin_type = parseInt(this.getUrlQuery('type'));
                 }
            })
        })
    </script>
</block>
