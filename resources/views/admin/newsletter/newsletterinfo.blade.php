@extends('admin.layouts.layout')
@section('content')
    <style>
        .ccs{
            width: calc(100%);
        }

    </style>
    <script src="{{loadEdition('/tinymce/js/tinymce/tinymce.min.js')}}"></script>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>Newsletter Info</h5>
            </div>
            <div class="ibox-title">
                电子报标题:{{$data['title']}}
            </div>
            <div class="ibox-content">
                <form class="form-horizontal" name="form"  method="post" action="{{route('newsletter.newsletter_list')}}" >
                           <?php echo $data['info'];?>
                    <div class="clearfix form-actions">
                        <div class="col-md-offset-3 col-md-9" style="margin-left: 45%">
                            <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回列表</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>

@endsection
