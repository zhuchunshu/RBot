<div class="card-body">
    <div class="mb-3">
        <label class="form-label">正向websocket连接地址</label>
        <input v-model="data.websocket" type="text" class="form-control">
        <small>当前:{{get_options("websocket","ws://127.0.0.1:6700")}}</small>
    </div>
    <div class="mb-3">
        <label class="form-label">http链接地址</label>
        <input v-model="data.http" type="text" class="form-control">
        <small>当前:{{get_options("http","http://127.0.0.1:5700")}}</small>
    </div>
    <div class="mb-3">
        <label class="form-label">连接秘钥 (access_token)</label>
        <input v-model="data.access_token" type="text" class="form-control">
    </div>
</div>