<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <div id="app" style="margin: 15px;" v-cloak>
        <p>支持同时上传 <span style="color: orangered;">{{uploadConfig.max_upload}}</span> 个文件 支持格式：<span style="color: orangered;">jpg,jpeg,gif,png,bmp</span></p>
        <el-upload
            ref="uploadRef"
            :limit="uploadConfig.max_upload"
            :action="uploadConfig.uploadUrl"
            :accept="uploadConfig.accept"
            :on-remove="handleRemove"
            :on-success="handleUploadSuccess"
            :on-error="handleUploadError"
            :on-exceed="handleExceed"
            multiple
            list-type="picture-card"
            class="thumb-uploader">
            <span class="el-icon-plus" style="font-size: 27px;color: #909399;"></span>
        </el-upload>
        <el-form ref="form" :model="form" label-width="100px" style="margin-top: 20px; ">
            <el-form-item label="上传至分组">
                <el-select v-model="cate_id" placeholder="请选择分组">
                    <template v-for="(item, index) in cate_list">
                        <el-option  :label="item.cate_name" :value="item.id"></el-option>
                    </template>
                </el-select>
            </el-form-item>
        </el-form>
        <el-button  style="margin-top:20px;"  @click="saveImageCate">确定</el-button>
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
                    activeName: 'uploadLocal',
                    uploadConfig: {
                        uploadUrl: "{:U('Upload/UploadAdminApi/uploadImage')}",
                        max_upload: 6,//同时上传文件数
                        accept: 'image/*' //接收的文件类型，请看：https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-accept
                    },
                    uploadedLocalFileList: [], //本地上传的文件
                    pagination: {
                        page: 1,
                        limit: 20,
                        total_pages: 0,
                        total_items: 0,
                    },
                    cate_list:[],
                    cate_id:'',
                    galleryList: [], //图库
                    form: {
                        cate_id:'',
                        search_date: [],
                        uid: '',
                        ip: '',
                        start_time: '',
                        end_time: '',
                        status: '',
                        sort_time: '',//排序：时间
                    }
                },
                watch: {},
                computed: {
                    selectdImageList: function () {
                        var result = [];
                        if (this.activeName == 'uploadLocal') {
                            this.uploadedLocalFileList.forEach(function (file) {
                                result.push({
                                    url: file.url,
                                    name: file.name
                                })
                            })
                        }

                        if (this.activeName == 'gallery') {
                            this.galleryList.forEach(function (file) {
                                if (file.is_select) {
                                    result.push({
                                        url: file.url,
                                        name: file.name
                                    })
                                }
                            })
                        }

                        return result;
                    }
                },
                filters: {
                    formatTime(timestamp) {
                        var date = new Date();
                        date.setTime(parseInt(timestamp) * 1000);
                        return moment(date).format('YYYY-MM-DD HH:mm:ss')
                    }
                },
                methods: {
                    handleTabClick: function () {
                        if (this.activeName == 'uploadLocal') {

                        }

                        if (this.activeName == 'gallery') {
                            this.getGalleryList();
                        }
                    },
                    handleRemove: function () {

                    },
                    saveImageCate: function () {
                        var that = this;
                        $.ajax({
                            url: "{:U('Upload/UploadAdminApi/saveImageToCate')}",
                            data: {img_list:that.uploadedLocalFileList,cate_id:that.form.cate_id    },
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                if(res.status){
                                    that.closePanel();
                                }
                            }
                        })
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
                    handleExceed: function(){
                        ELEMENT.Message.error('上传文件数量超限制');
                    },
                    getGalleryList: function () {
                        var that = this;
                        var where = {
                            page: this.pagination.page,
                            limit: this.pagination.limit,
                        };
                        $.ajax({
                            url: "{:U('Upload/UploadAdminApi/getGalleryList')}",
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
                    selectImgEvent: function (index) {
                        this.galleryList[index].is_select = !this.galleryList[index].is_select
                    },
                    confirm: function(){
                        event = new CustomEvent('ZTBCMS_UPLOAD_FILE', {
                            detail: {
                                files: this.selectdImageList
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
                    getImageCate: function () {
                        var that = this;
                        $.ajax({
                            url: "{:U('Upload/UploadAdminApi/getImageCate')}",
                            data: {},
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                that.cate_list = res.data.cate_list;
                                that.cate_id = res.data.cate_list[0]['id'];

                            }
                        })
                    }
                },
                mounted: function () {
                    this.getImageCate();
                    this.uploadConfig.max_upload = parseInt(this.getUrlQuery('max_upload') || this.uploadConfig.max_upload);
                }
            })
        })
    </script>

</block>
