@extends('admin.layouts.app')

@section('title', 'Activities')

@section('content')

<div class="container mx-auto px-4 py-6">

    <div class="overflow-x-auto pt-2">
      <table class="min-w-full bg-white border border-gray-200">
        <thead>
          <tr class="bg-gray-100 text-center">
            <th class="py-2 px-4 border-b w-[50px] text-center">
              <input type="checkbox" id="selectAll" class="cursor-pointer">
            </th>
            <th class="py-2 px-4 border-b w-[50px] text-center">No</th>
            <th class="py-2 px-4 border-b w-[50px]">ID</th>
            <th class="py-2 px-4 border-b w-[200px]">Activity Name</th>
            <th class="py-2 px-4 border-b w-[400px]">Description</th>
            <!-- <th class="py-2 px-4 border-b w-[200px]">Update At</th> -->
            <th class="py-2 px-4 border-b w-[200px]">Action</th>
          </tr>
        </thead>
        <tbody id="activityTable">
          @foreach($activities as $activity)
          <tr class="cursor-pointer hover:bg-gray-100 transition" >
            <td class="py-2 px-4 border-b text-center">
              <input type="checkbox" name="selected[]" value="{{ $activity->id }}" class="rowCheckbox cursor-pointer">
            </td>
            <td class="py-2 px-4 border-b text-center">{{ $loop->iteration }}</td>
            <td class="py-2 px-4 border-b">{{$activity->id}}</td>
            <td class="py-2 px-4 border-b">{{$activity->name}}</td>
            <td class="py-2 px-4 border-b ">{{$activity->description}}</td>
            <!-- <td class="py-2 px-4 border-b">{{$activity->updated_at}}</td> -->
            <td class="py-2 px-4 border-b text-center">
              <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" onsubmit="return confirm('Are you sure want to delete this?')">
                <a href="{{ route('admin.groups', $activity->id) }}"
                class="text-blue-600 hover:text-blue-800">Detail</a> |
                <a href="{{ route('activities.edit', $activity->id) }}"
                 class="text-blue-600 hover:text-blue-800">Edit</a> |
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>



@endsection
