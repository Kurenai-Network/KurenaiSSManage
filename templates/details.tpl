<script src="https://cdn.tailwindcss.com/"></script>
<link href="https://cdn.staticfile.org/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<script src="https://cdn.staticfile.org/clipboard.js/2.0.8/clipboard.js"></script>
<script src="https://cdn.staticfile.org/layer/3.5.1/layer.min.js"></script>

<!--Overlay Effect-->
<div class="fixed hidden inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="mask"></div>
<div id="kurenai_ss_manage" systemurl="{$systemurl}">
    <div>

        <div class="min-w-full font-mono mx-auto">
            <div class="bg-white shadow-2xl p-6 text-gray-700 min-h-full">
                <!--user info-->
                <div class="flex flex-wrap w-full mb-6">
                    <div class="w-full mb-6 lg:mb-0">
                        <h1 class="sm:text-md text-2xl font-medium title-font mb-2 text-gray-900">Information</h1>
                        <div class="h-1 w-20 bg-indigo-500 rounded"></div>
                    </div>
                </div>
                <!-- alert start -->
                <!--                <div class="text-center">-->
                <!--                    <div>-->
                <!--                        <div class="inline-flex items-center bg-gray-100 leading-none text-pink-600 rounded-full p-2 shadow text-teal text-sm">-->
                <!--                            <span class="inline-flex bg-pink-600 text-white rounded-full h-6 px-3 justify-center items-center">Welcome</span>-->
                <!--                            <span class="inline-flex px-2">感谢你这个小逼崽子使用我们的服务</span>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->
                <!-- alert end -->
                <div class="relative ">
                    <div class="px-8">
                        <div class="">
                            <h2 class="text-4xl font-bold leading-7 text-gray-900 sm:text-2xl sm:truncate">
                                {$product}<sub>{$serviceid}</sub>
                            </h2>
                            <div class="mt-1 flex flex-col">
                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                    <i class="fa-solid fa-bolt w-5"></i>
                                    {$user['uuid']}
                                    <div class="pl-2">
                                        <button onclick="reset_uuid()" class=" inline-flex items-center p-1 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <i class="fa-solid fa-arrow-rotate-right text-gray-500"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                    <i class="fa-solid fa-calendar w-5"></i>
                                    {$nextduedate}
                                </div>
                            </div>
                        </div>
                        <div class="lg:absolute right-0 inset-y-0 lg:mr-8 pt-6 grid lg:grid-cols-3 lg:gap-3 gap-2 grid-cols-2">
                            <span class="">
                                <button class="open-btn-1 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fa-solid fa-arrow-rotate-right w-5 -ml-1 mr-2 text-gray-500 fa-lg"></i>
                                    {L::client_reset}
                                </button>
                            </span>

                            <span class="">
                                <button onclick="upgrade()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fa-solid fa-circle-up w-5 -ml-1 mr-2 text-gray-500 fa-lg"></i>
                                    {L::client_upgrade}
                                </button>
                            </span>

                            <span class="">
                                <button onclick="renewal()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fa-solid fa-repeat w-5 -ml-1 mr-2 text-gray-500 fa-lg"></i>
                                    {L::client_renewal}
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <!--user info end-->
                <!--statistic start-->
                <section class="text-gray-600 body-font">
                    <div class="mx-auto">
                        <div class="flex flex-wrap w-full my-6">
                            <div class="w-full mb-6 lg:mb-0">
                                <h1 class="sm:text-md text-2xl font-medium title-font mb-2 text-gray-900">Statistic</h1>
                                <div class="h-1 w-20 bg-indigo-500 rounded"></div>
                            </div>
                        </div>
                        <div class="text-center grid lg:grid-cols-4 lg:gap-4 md:grid-cols-2 md:gap-2">
                            <div class="p-2">
                                <div class="bg-indigo-500 rounded-lg p-2 xl:p-6">
                                    <h2 class="title-font font-medium text-xl text-white">{$user['bandwidth']}</h2>
                                    <p class="leading-relaxed text-gray-100 text-sm font-bold"><i class="fa-solid fa-bolt text-gray-100 w-8"></i>{L::common_total}</p>
                                </div>
                            </div>
                            <div class="p-2">
                                <div class="bg-indigo-500 rounded-lg p-2 xl:p-6">
                                    <h2 class="title-font font-medium text-xl text-white">{$user['total_used']}</h2>
                                    <p class="leading-relaxed text-gray-100 text-sm font-bold"><i class="fa-solid fa-table-columns text-gray-100 w-8"></i>{L::common_used}</p>
                                </div>
                            </div>
                            <div class="p-2">
                                <div class="bg-indigo-500 rounded-lg p-2 xl:p-6">
                                    <h2 class="title-font font-medium text-xl text-white">{$user['upload']}</h2>
                                    <p class="leading-relaxed text-gray-100 text-sm font-bold"><i class="fa-solid fa-angle-up text-gray-100 w-8"></i>{L::common_upload}</p>
                                </div>
                            </div>
                            <div class="p-2">
                                <div class="bg-indigo-500 rounded-lg p-2 xl:p-6">
                                    <h2 class="title-font font-medium text-xl text-white">{$user['download']}</h2>
                                    <p class="leading-relaxed text-gray-100 text-sm font-bold"><i class="fa-solid fa-angle-down w-5 text-gray-100 w-8"></i>{L::common_download}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!--statistic end-->
                <div class="flex flex-wrap w-full my-6">
                    <div class="w-full mb-6 lg:mb-0">
                        <h1 class="sm:text-md text-2xl font-medium title-font mb-2 text-gray-900">Subscribe</h1>
                        <div class="h-1 w-20 bg-indigo-500 rounded"></div>
                    </div>
                </div>
                <div class="px-7 bg-white shadow-lg rounded-2xl">
                    <div class="grid lg:grid-cols-3 lg:gap-3 sm:grid-cols-2 sm:gap-2">
                        <div class="flex-1 group">
                            <a data-clipboard-text="https://{$subscribe_url}/sub?token={$user['uuid']}&sid={$user['sid']}&type=ss" class="copy flex items-end justify-center text-center mx-auto px-4 pt-2 w-full text-gray-400 group-hover:text-indigo-500">
                                <span class="block p-1">
                                    <img src="https://cdn.jsdelivr.net/gh/Kurenai-Network/staticfile/Shadowsocks.svg" class="ml-8 block" height="30px" width="30px" alt="Shadowsocks">
                                    <span class="block pb-2">Shadowsocks</span>
                                    <span class="block w-5 mx-auto h-1 group-hover:bg-indigo-500 rounded-full"></span>
                                </span>
                            </a>
                        </div>
                        <div class="flex-1 group">
                            <a id="Clash" onclick="set_url(this)" data-clipboard-text="https://{$subscribe_url}/sub?token={$user['uuid']}&sid={$user['sid']}&type=clash" class="open-btn-2 flex items-end justify-center text-center mx-auto px-4 pt-2 w-full text-gray-400 group-hover:text-indigo-500">
                                <span class="block p-1">
                                    <img src="https://cdn.jsdelivr.net/gh/Kurenai-Network/staticfile/Clash.svg" class="ml-1 block" height="30px" width="30px" alt="Clash">
                                    <span class="block pb-2">Clash</span>
                                    <span class="block w-5 mx-auto h-1 group-hover:bg-indigo-500 rounded-full"></span>
                                </span>
                            </a>
                        </div>
                        <div class="flex-1 group">
                            <a id="Surge" onclick="set_url(this)" data-clipboard-text="https://{$subscribe_url}/sub?token={$user['uuid']}&sid={$user['sid']}&type=surge" class="open-btn-2 flex items-end justify-center text-center mx-auto px-4 pt-2 w-full text-gray-400 group-hover:text-indigo-500">
                                <span class="block p-1">
                                    <img src="https://cdn.jsdelivr.net/gh/Kurenai-Network/staticfile/Surge.svg" class="ml-1 block" height="30px" width="30px" alt="Surge">
                                    <span class="block pb-2">Surge</span>
                                    <span class="block w-5 mx-auto h-1 group-hover:bg-indigo-500 rounded-full"></span>
                                </span>
                            </a>
                        </div>
                        <div class="flex-1 group">
                            <a data-clipboard-text="https://{$subscribe_url}/sub?token={$user['uuid']}&sid={$user['sid']}&type=nodelist" class="copy flex items-end justify-center text-center mx-auto px-4 pt-2 w-full text-gray-400 group-hover:text-indigo-500">
                                <span class="block p-1">
                                    <img src="https://cdn.jsdelivr.net/gh/Kurenai-Network/staticfile/Surge NodeList.svg" class="ml-11 block" height="30px" width="30px" alt="Surge NodeList">
                                    <span class="block pb-2">Surge NodeList</span>
                                    <span class="block w-5 mx-auto h-1 group-hover:bg-indigo-500 rounded-full"></span>
                                </span>
                            </a>
                        </div>
                        <div class="flex-1 group">
                            <a id="Shadowrocket" onclick="set_url(this)" data-clipboard-text="https://{$subscribe_url}/sub?token={$user['uuid']}&sid={$user['sid']}&type=shadowrocket" class="open-btn-2 flex items-end justify-center text-center mx-auto px-4 pt-2 w-full text-gray-400 group-hover:text-indigo-500">
                                <span class="block p-1">
                                    <img src="https://cdn.jsdelivr.net/gh/Kurenai-Network/staticfile/Shadowrocket.svg" class="ml-9 block" height="30px" width="30px" alt="Shadowrocket">
                                    <span class="block pb-2">Shadowrocket</span>
                                    <span class="block w-5 mx-auto h-1 group-hover:bg-indigo-500 rounded-full"></span>
                                </span>
                            </a>
                        </div>
                        <div class="flex-1 group">
                            <a id="Quantumult X" onclick="set_url(this)" data-clipboard-text="https://{$subscribe_url}/sub?token={$user['uuid']}&sid={$user['sid']}&type=qx" class="open-btn-2 flex items-end justify-center text-center mx-auto px-4 pt-2 w-full text-gray-400 group-hover:text-indigo-500">
                                <span class="block p-1">
                                    <img src="https://cdn.jsdelivr.net/gh/Kurenai-Network/staticfile/Quantumult%20X.svg" class=" ml-9 block" height="30px" width="30px" alt="Quantumult X">
                                    <span class="block pb-2">Quantumult X</span>
                                    <span class="block w-5 mx-auto h-1 group-hover:bg-indigo-500 rounded-full"></span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
                <!--node info start-->
                <!--                <div class="flex flex-col mb-8">-->
                <!--                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">-->
                <!--                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">-->
                <!--                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">-->
                <!--                                <table class="min-w-full divide-y divide-gray-200">-->
                <!--                                    <thead class="bg-gray-50">-->
                <!--                                    <tr>-->
                <!--                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">-->
                <!--                                            {L::client_node_name}-->
                <!--                                        </th>-->
                <!--                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">-->
                <!--                                            {L::client_node_status}-->
                <!--                                        </th>-->
                <!--                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">-->
                <!--                                            {L::client_bandwidth}-->
                <!--                                        </th>-->
                <!--                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">-->
                <!--                                            {L::client_node_rate}-->
                <!--                                        </th>-->
                <!--                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">-->
                <!--                                            {L::client_node_tag}-->
                <!--                                        </th>-->
                <!--                                    </tr>-->
                <!--                                    </thead>-->
                <!---->
                <!--                                </table>-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->
                <!--node info end-->
            </div>
        </div>
    </div>
</div>
<!-- dialog_1 -->
<div class="dialog-1 hidden">
    <div class="fixed flex justify-center items-center inset-0 z-50 outline-none focus:outline-none backdrop-blur-sm">
        <div class="flex flex-col p-8 bg-white shadow-md hover:shodow-lg rounded-2xl dialog-mark-1">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-16 h-16 rounded-2xl p-3 border border-blue-100 text-blue-400 bg-blue-50" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>

                    <div class="flex flex-col ml-3">
                        <div class="font-medium leading-none">{L::client_question_reset_bandwidth}?</div>
                        <p class="text-sm text-gray-600 leading-none mt-1">{L::client_warn_reset_bandwidth}
                        </p>
                    </div>
                </div>
                <button onclick="reset_bandwidth()" class="close-btn-1 flex-no-shrink bg-red-500 px-4 ml-4 py-2 text-sm shadow-sm hover:shadow-lg font-medium tracking-wider border-2 border-red-500 text-white rounded-full">确定</button>
            </div>
        </div>
    </div>
</div>
<div class="dialog-2 hidden">
    <div class="fixed flex justify-center items-center inset-0 z-50 outline-none focus:outline-none backdrop-blur-sm">
        <div class="flex flex-col p-8 bg-white shadow-md hover:shodow-lg rounded-2xl dialog-mark-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="grid gap-2 grid-cols-2">
                        <span class="" id="copy-span">
                            <button  id="copy-button" class="copy inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fa-solid fa-copy w-5 -ml-1 mr-2 text-gray-500 fa-lg"></i>
                                {L::client_copy}
                            </button>
                        </span>

                        <span class="">
                            <button id="one_click" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fa-solid fa-file-import w-5 -ml-1 mr-2 text-gray-500 fa-lg"></i>
                                {L::client_import}
                            </button>
                        </span>
                        <span class="hidden" id="Choc">
                            <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fa-solid fa-file-import w-5 -ml-1 mr-2 text-gray-500 fa-lg"></i>
                                {L::client_import_to_choc}
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const dialog_2 = document.querySelector('.dialog-2');
    var one_click = document.getElementById('one_click');
    var copy_span = document.getElementById('copy-span');
    var copy_button = document.getElementById('copy-button');
    var import_to_choc = document.getElementById('Choc');
    function set_url(obj){
        var subscribe_url = obj.getAttribute('data-clipboard-text');
        dialog_2.classList.remove('hidden')
        function open_new(url){
            one_click.addEventListener('click',function(){
                console.log(url)
                window.location.replace(url)
            },{
                once: true
            });
        }
        copy_button.setAttribute('data-clipboard-text', subscribe_url);
        switch (true) {
            case obj.id === 'Clash':
                open_new('clash://install-config?url=' + encodeURIComponent(subscribe_url));
                import_to_choc.classList.remove('hidden');
                import_to_choc.addEventListener('click',function(){
                    console.log('import to choc')
                    window.location.replace('choc://install-config?url=' + encodeURIComponent(subscribe_url))
                },{
                    once: true
                });
                copy_button.innerHTML = '<i class="fa-solid fa-copy w-5 -ml-1 mr-2 text-gray-500 fa-lg"></i>{L::client_copy_subscribe_url}'
                one_click.innerHTML = '<i class="fa-solid fa-file-import w-5 -ml-1 mr-2 text-gray-500 fa-lg"></i>{L::client_import_to_clash}'
                copy_span.classList.add('flex', 'justify-center', 'col-span-2')
                break;
            case obj.id === 'Surge':
                open_new('surge:///install-config?url=' + encodeURIComponent(subscribe_url));
                break;
            case obj.id === 'Quantumult X':
                var subscribe_json = {
                    "server_remote": [
                        subscribe_url + ", tag=Kurenai Network, update-interval=172800, opt-parser=false, enabled=true, img-url=https://raw.githubusercontent.com/Kurenai-Network/staticfile/main/kurenai_quantumultx.png"
                    ]
                }
                open_new('quantumult-x:///update-configuration?remote-resource=' + encodeURIComponent(JSON.stringify(subscribe_json)));
                break;
            case obj.id === 'Shadowrocket':
                open_new('shadowrocket://add/sub://' + btoa(subscribe_url) + '#Kurenai Network');
                break;
        }

    }
    $(document).mouseup(function(e){
        var _con = $('.dialog-mark-1');   // 设置目标区域
        if(!_con.is(e.target) && _con.has(e.target).length === 0){ // Mark 1
            dialog_1.classList.add('hidden')
        }
    });
    $(document).mouseup(function(e){
        var _con = $('.dialog-mark-2');   // 设置目标区域
        if(!_con.is(e.target) && _con.has(e.target).length === 0){ // Mark 1
            dialog_2.classList.add('hidden');
            import_to_choc.classList.add('hidden');
            copy_span.classList.remove('flex', 'justify-center', 'col-span-2');
            copy_button.innerHTML = '<i class="fa-solid fa-copy w-5 -ml-1 mr-2 text-gray-500 fa-lg"></i>{L::client_copy}';
            one_click.innerHTML = '<i class="fa-solid fa-file-import w-5 -ml-1 mr-2 text-gray-500 fa-lg"></i>{L::client_import}';
        }
    });
    function reset_bandwidth() {
        $.ajax({
            type:"GET",
            url:'clientarea.php?action=productdetails&id={$serviceid}&KurenaiSSManageAction=ResetBandwidth&sid={$serviceid}',
            datatype: "json",
            success:function(data){
                var obj = JSON.parse(data);
                if (obj['status'] === 'success'){
                    layer.msg(obj['msg']);
                    location.reload();
                }else{
                    layer.msg(obj['msg'])
                }
            },
            error: function(){
                layer.msg('{L::common_error}')
            }
        });
    }
    function reset_uuid() {
        $.ajax({
            type:"GET",
            url:'clientarea.php?action=productdetails&id={$serviceid}&KurenaiSSManageAction=ResetUUID&sid={$serviceid}',
            datatype: "json",
            success:function(data){
                var obj = JSON.parse(data);
                if (obj['status'] === 'success'){
                    layer.msg(obj['msg']);
                    location.reload();
                }else{
                    layer.msg(obj['msg'])
                }
            },
            error:function(){
                layer.msg('{L::common_error}')
            }
        });
    }
    function upgrade() {
        window.location.href='upgrade.php?type=package&id={$serviceid}'
    }
    function renewal(){
        window.location.href='index.php?m=renewal&action=renew&sid={$serviceid}'
    }
    var clipboard = new ClipboardJS('.copy');
    clipboard.on('success', function (e) {
        layer.msg('{L::client_copy_success}')
        console.log(e);
    });
    clipboard.on('error', function (e) {
        console.log(e);
    });
    const dialog_1 = document.querySelector('.dialog-1');
    const open_btn_1 = document.querySelector('.open-btn-1');
    const close_btn_1 = document.querySelectorAll('.close-btn-1');

    open_btn_1.addEventListener('click', function (){
        dialog_1.classList.remove('hidden')
    });

    close_btn_1.forEach(close => {
        close.addEventListener('click', function (){
            dialog_1.classList.add('hidden')
        });
    });
</script>

