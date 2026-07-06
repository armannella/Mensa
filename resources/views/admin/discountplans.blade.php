@extends('layout.master')

@section('header' , 'Discounts')

@section('content')
    <div class="row d-flex justify-content-between  mt-4">
        <div class="col-md-4 ">
            <div class="row ">
                <div>
                    <form action="{{ route('admin.discounts.store') }}" method="post">
                        @csrf
                        <x-input name="name" label="Name of Plan" placeholder="Plan name" required/>
                        <x-input type="number" name="percentage" label="Discount percent" placeholder="From 1 to 100" min='1' max='100' required/>
                        <x-input name="description" label="description" placeholder="optional"/>
                        <x-button color="bgc-green">ADD !</x-button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="row">
                

                <div>
                    <x-table :headers="['Plan Name', 'Percentage', 'Description', 'Actions']">
                        @forelse ($discountPlans as $plan)
                            <tr>
                                <td>{{$plan->name}}</td>
                                <td>{{$plan->percentage}}</td>
                                <td>{{$plan->description}}</td>
                                <td>
                                    <div class="d-flex justify-content-center column-gap-2">
                                        <x-actionbtn type="button" color="bgc-lime" class="edit-btn"
                                                                                    data-id="{{ $plan->id }}"
                                                                                    data-url="{{ route('admin.discounts.edit', $plan->id) }}"
                                                                                    data-name="{{ $plan->name }}"
                                                                                    data-percent="{{ $plan->percentage }}"
                                                                                    data-desc="{{ $plan->description }}"
                                                                                    data-bs-toggle="modal" data-bs-target="#editModal">
                                            Edit
                                        </x-actionbtn>

                                    
                                        <form action="{{ route('admin.discounts.delete', $plan->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-actionbtn type="submit" color="bgc-red">Delete</x-actionbtn>
                                        </form>
                                    <div>
                                </td>
                            </tr>
                        @empty
                            <td colspan="4" class="text-center py-4 text-white">No plans available yet.</td>
                        @endforelse
                    </x-table>
                </div>
            </div>
        </div>
    </div>



    <x-modal id="editModal" title="Edit Discount Plan">
        <form id="editForm" method="POST" action="">
            @csrf
            @method('PUT')
            
            <x-input id="edit_name" name="name" label="Name of Plan" required />
            <x-input type="number" id="edit_percentage" name="percentage" label="Discount percent" min="1" max="100" required />
            <x-input id="edit_description" name="description" label="Description" />
            
            <div class="mt-4">
                <x-button color="bgc-lime">Save Changes</x-button>
            </div>
        </form>
    </x-modal>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
           
            const editButtons = document.querySelectorAll('.edit-btn');
            const editForm = document.getElementById('editForm');
            
            
            const editName = document.getElementById('edit_name');
            const editPercentage = document.getElementById('edit_percentage');
            const editDesc = document.getElementById('edit_description');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    
                    const id = this.getAttribute('data-id');
                    editForm.action = this.getAttribute('data-url');


                    
                    editName.value = this.getAttribute('data-name');
                    editPercentage.value = this.getAttribute('data-percent');
                    editDesc.value = this.getAttribute('data-desc');
                });
            });
        });
    </script>
@endsection