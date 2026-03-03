@extends('admin.layouts.app')

@section('title', 'Edit Activity')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white shadow-md rounded px-8 pt-6 pb-8">
  <h2 class="text-2xl font-semibold mb-6 text-center">Edit User Profile</h2>

  <form action="{{ route('admin.datauser.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-4">
      <label for="name" class="block text-gray-700 font-bold mb-2">ID:</label>
      <input readonly type="text" name="id" id="id" value="{{ old('id', $user->id) }}"
             class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
      @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="mb-4">
      <label for="name" class="block text-gray-700 font-bold mb-2">Username:</label>
      <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}"
             class="validate-required shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
      @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="mb-4">
      <label for="name" class="block text-gray-700 font-bold mb-2">Email:</label>
      <input type="email" name="email" id="email" value="{{ old('email', $user->profile->email) }}"
             class="validate-email shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
      @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="mb-4">
      <label for="name" class="block text-gray-700 font-bold mb-2">Fullname:</label>
      <input type="text" name="fullname" id="fullname" value="{{ old('fullname', $user->profile->fullname) }}"
             class="validate-required shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
      @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>
    
    <div class="mb-4">
      <label for="no_handphone" class="block text-gray-700 font-bold mb-2">No Handphone:</label>
      <input type="text" name="no_handphone" id="no_handphone" value="{{ old('no_handphone', $user->profile->no_handphone) }}"
             class="validate-number shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
      @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>
    
    <div class="mb-4">
      <label for="activity" class="block text-gray-700 font-bold mb-2">Activity</label>
      <select name="activity" required class="validate-required activity-dropdown border rounded px-4 py-2 w-full">
        <option value="">Choose Activity</option>
          @foreach($activities as $activity)
              <option value="{{ $activity->id }}" 
                {{ (old('activity') ?? ($user->profile->activity_id ?? '')) == $activity->id ? 'selected' : '' }}>
                {{ $activity->name }}
              </option>
          @endforeach
      </select>
      @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>
    
    <div class="mb-4">
      <label for="group" class="block text-gray-700 font-bold mb-2">Group</label>
        <select name="group" class="validate-required group-dropdown border rounded px-4 py-2 w-full">
          <option value="">Choose Group</option>
        </select>
      @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>
    
    <div class="mb-4">
      <label for="name" class="block text-gray-700 font-bold mb-2">Unit</label>
        <select name="unit" class="validate-required unit-dropdown border rounded px-4 py-2 w-full">
          <option value="">Choose Unit</option>
        </select>
      @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="flex justify-between items-center">
      <a href="{{ route('admin.datauser') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
        Batal
      </a>
      <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
        Simpan Perubahan
      </button>
    </div>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function(){

      let selectedActivity = "{{ old('activity', $user->profile->activity_id ?? '') }}";
      let selectedGroup    = "{{ old('group', $user->profile->group_id ?? '') }}";
      let selectedUnit     = "{{ old('unit', $user->profile->unit_id ?? '') }}";

      let $activity = $('.activity-dropdown');
      let $group    = $('.group-dropdown');
      let $unit     = $('.unit-dropdown');

      function clearSelectError($element) {
          $element.removeClass('border-red-500');
          $element.parent().find('.input-error').remove();
      }
 
      function loadGroups(activityID, selectedGroup = null) {

          $group.html('<option value="">Choose Group</option>');
          $unit.html('<option value="">Choose Unit</option>');

          if(activityID){
              $.ajax({
                  url: '/get-groups/' + activityID,
                  type: 'GET',
                  dataType: 'json',
                  success: function(groups){

                      $.each(groups, function(i, group){

                          let selected = (selectedGroup == group.id) ? 'selected' : '';

                          $group.append(
                              '<option value="'+group.id+'" '+selected+'>'+group.name+'</option>'
                          );
                      });

                      
                      if(selectedGroup){
                          clearSelectError($group);
                          loadUnits(selectedGroup, selectedUnit);
                      }
                      
                  }
              });
          }
      }

      function loadUnits(groupID, selectedUnit = null) {

          $unit.html('<option value="">Choose Unit</option>');

          if(groupID){
              $.ajax({
                  url: '/get-units/' + groupID,
                  type: 'GET',
                  dataType: 'json',
                  success: function(units){

                      $.each(units, function(i, unit){

                          let selected = (selectedUnit == unit.id) ? 'selected' : '';

                          $unit.append(
                              '<option value="'+unit.id+'" '+selected+'>'+unit.name+'</option>'
                          );
                      });

                      if(selectedUnit){
                          clearSelectError($unit);
                      }
                     
                  }
              });
          }
      }


      $activity.on('change', function(){
          loadGroups($(this).val());
      });

      $group.on('change', function(){
          loadUnits($(this).val());
      });


      if(selectedActivity){
          loadGroups(selectedActivity, selectedGroup);
      }

  });
</script>
@endsection

