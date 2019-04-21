<extend name="../../Admin/View/Common/element_layout"/>
<block name="title"></block>

<block name="content">
    <div id="app" v-cloak>
        <div class="wrapper">
            <div class="content-wrapper" style="margin-left:0;width:100%;">
                <section class="content">

                    <el-card  style="margin-bottom: 10px;" >
                        <!-- 基本信息-->
                        <el-form ref="form" :model="postData" label-width="80px">
                            <div class="col-sm-2 col-md-2 col-lg-2 padding-l_0">

                                    <el-select   v-model="cate_id"  style="width: 100%;">
                                        <template v-for="(item,index) in group_list">
                                            <el-option :label="item.cate_name" :value="item.id"></el-option>
                                        </template>
                                    </el-select>
                            </div>

                        </el-form>
                        <el-button style="margin-left: 20px;margin-top: 20px;"type="primary" @click="confirm" >保存</el-button>

                    </el-card>

            </div>


        </div>
        </section>
    </div>

</block>

<block name="footer">

    <script>
        $(document).ready(function () {

            new Vue({
                el: "#app",
                data: function () {
                    return {
                        cate_id:'1',
                        postData:{},
                        group_list:[]
                    };
                },
//                mixins: [window.__methods,window.__filters],
                mounted: function () {
                    this.getGroups();
                },

                methods: {
                    closeIframe: function () {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                    },
                    getGroups: function () {
                        var that = this;
                        var url = '{:U("Upload/UploadAdminApi/getGroups")}';
                        var data = {};
                        that.httpGet(url,data, function (res) {
                            if(res.status){
                                that.group_list = res.data;
                            }
                        });

                    },

                    closePanel: function(){
                        if(parent.window.layer){
                            parent.window.layer.closeAll();
                        }else{
                            window.close();
                        }
                    },
                    confirm: function () {
                        var that = this;
                        event = new CustomEvent('MOVE_GROUP', {
                            detail: {
                                files: that.cate_id
                            }
                        })
                        window.parent.dispatchEvent(event);
                        this.closePanel();
                    },
                },


            });
        });
    </script>

</block>