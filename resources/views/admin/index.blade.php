@extends("app")
@section('title','Admin')
@section('content')
    <div class="col-md-4">
        <div class="card border-0">
            <div class="card-body">
                <h3 class="card-title">二维码展示区</h3>
                <div class="ratio ratio-1x1 placeholder placeholder-wave">
                    <div class="placeholder-image" id="qrcode"></div>
                </div>
                <small>有二维码则显示</small>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0">
            <div class="card-body">
                <h3 class="card-title">BOT Server logger</h3>
                <pre id="logger" style="height: 400px;">
                    loadding...
                </pre>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{mix("js/admin/logger.js")}}"></script>
@endsection