@foreach($files as $file)
<div class="filename" id="attachment-{{$file->id}}">
<span><a href="{{url('download/'.$file->name)}}">{{$file->name}}</a><span class="close" onclick="removeFile({{$file->id}})" data-dismiss="alert">&times;</span></span>
</div>
@endforeach