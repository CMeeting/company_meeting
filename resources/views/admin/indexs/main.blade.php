@extends('admin.layouts.layout')

@section('title', 'Dashboard')

@section('css')
    <link href="{{loadEdition('/admin/css/pxgridsicons.min.css')}}" rel="stylesheet"/>
@endsection
@section('content')
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading bm0">
                <span><strong>{{$now}}</strong></span>
                <span class="tools pull-right">
              <a class="icon-chevron-down" href="javascript:;"></a>
          </span>
            </header>
            <div class="panel-body" id="panel-bodys" style="display: block;">
                <table class="table table-hover personal-task">
                    <tbody>
                    <tr>
                        <td>
                            <strong>Aujourd'hui:</strong>Partiellement nuageux avec des orages.
                        </td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop
