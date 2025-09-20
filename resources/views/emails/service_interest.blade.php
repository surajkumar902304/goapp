<h2>Service Interest Submission</h2>

<p><strong>Name:</strong> {{ $data['name'] }}</p>
<p><strong>Phone:</strong> {{ $data['phone'] }}</p>
<p><strong>Email:</strong> {{ $data['email'] }}</p>
<p><strong>Note:</strong> {{ $data['note'] }}</p>

<hr>

<h3>Service Details</h3>
<p><strong>Title:</strong> {{ $data['service_title'] }}</p>
<p><strong>Subtitle:</strong> {{ $data['service_subtitle'] }}</p>
<p><strong>Description:</strong> {{ $data['service_desc'] }}</p>
@if($data['service_image'])
    <p><strong>Image:</strong> <br><img src="{{ $data['service_image'] }}" alt="Service Image" style="max-width: 100%;"></p>
@endif
