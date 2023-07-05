@extends('layouts.app')

@section('content')
<script src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap" async defer></script>

<div class="container">
    <res-map></res-map>
</div>
@endsection
<style>
    #map {
      height: 480px; 
      width: 100%;
    }
  </style>
  