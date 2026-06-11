@extends(Auth::user()->role == 'manager' ? 'layouts.manager' : 'layouts.app')

@section('content')

<div class="container mt-4">

<div class="card shadow border-0">

<div class="card-header bg-dark text-white">

<h3>

Profile User

</h3>

</div>

<div class="card-body">

@if(session('success'))

<div class="alert alert-success">

{{ session('success') }}

</div>

@endif

<form
method="POST"
action="{{ route('profile.update') }}"
enctype="multipart/form-data">

@csrf
@method('PATCH')

<div class="text-center mb-4">

@if($user->foto)

<img
src="{{ asset('storage/'.$user->foto) }}"
width="150"
class="rounded-circle shadow">

@else

<img
src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
width="150"
class="rounded-circle shadow">

@endif

</div>

<div class="mb-3">

<label>Foto Profile</label>

<input
type="file"
name="foto"
class="form-control">

</div>

<div class="mb-3">

<label>Nama</label>

<input
type="text"
name="name"
class="form-control"
value="{{ $user->name }}">

</div>

<div class="mb-3">

<label>Email</label>

<input
type="email"
name="email"
class="form-control"
value="{{ $user->email }}">

</div>

<div class="mb-3">

<label>Role</label>

<input
type="text"
class="form-control"
value="{{ $user->role }}"
readonly>

</div>

<div class="mb-3">

<label>Jabatan</label>

<input
type="text"
name="jabatan"
class="form-control"
value="{{ $user->jabatan }}">

</div>

<div class="mb-3">

<label>Divisi</label>

<input
type="text"
name="divisi"
class="form-control"
value="{{ $user->divisi }}">

</div>

<button
class="btn btn-primary">

Update Profile

</button>

</form>

<hr>

<div class="row text-center">

<div class="col-md-4">

<div class="card bg-primary text-white">

<div class="card-body">

<h4>

{{ $totalCutting }}

</h4>

Produksi Cutting

</div>

</div>

</div>

<div class="col-md-4">

<div class="card bg-danger text-white">

<div class="card-body">

<h4>

{{ $totalCrimping }}

</h4>

Produksi Crimping

</div>

</div>

</div>

<div class="col-md-4">

<div class="card bg-success text-white">

<div class="card-body">

<h4>

{{ $totalLine }}

</h4>

Produksi Line

</div>

</div>

</div>

</div>

</div>

</div>

</div>

@endsection