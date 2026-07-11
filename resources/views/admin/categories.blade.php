@extends('layout.master')

@section('header' , 'Categories')

@section('content')
    <div class="row d-flex justify-content-between  mt-4">
        <div class="col-md-4 ">
            <div class="row ">
                <div>
                    <form action="{{ route('admin.categories.store') }}" method="post">
                        
                        @csrf
                
                        <x-input name="name" label="Category Name" placeholder="e.g. Primo" required />
                        <x-input type="number" name="price" label="Base Price" placeholder="3.20" step="0.01" min="0" max="10" required/>

                        <x-button color="bgc-green">ADD !</x-button>
            
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="row">
                

                <div>
                    <x-table :headers="['name', 'Price' , 'Actions']">
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{$category->name}}</td>
                                <td>{{$category->price}}</td>
                                
                                
                                <td>
                                    <div class="d-flex justify-content-center column-gap-2">
                                        <x-actionbtn type="button" color="bgc-lime" class="edit-btn"
                                                                                    data-id="{{ $category->id }}"
                                                                                    data-url="{{ route('admin.categories.update', $category->id) }}"
                                                                                    data-name="{{ $category->name }}"
                                                                                    data-price="{{ $category->price }}"
                                                                                    data-bs-toggle="modal" data-bs-target="#editModal">
                                            Edit
                                        </x-actionbtn>

                                    
                                        <form action="{{ route('admin.categories.delete', $category->id) }}" method="POST" onsubmit=" return confirm('Are you sure you want to delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-actionbtn type="submit" color="bgc-red">Delete</x-actionbtn>
                                        </form>
                                    <div>
                                </td>
                            </tr>
                        @empty
                            <td colspan="4" class="text-center py-4 text-white">No Categories available yet.</td>
                        @endforelse
                    </x-table>
                </div>
            </div>
        </div>
    </div>



 <x-modal id="editModal" title="Edit Category">
        <form id="editForm" method="POST" action="">
            @csrf
            @method('PUT')
            
            <x-input id="edit_name" name="name" label="Name of Category" required />
            <x-input type="number" id="edit_price" name="price" label="Base Price" step="0.01" min="0" max="10" required />
            
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
            const editPrice = document.getElementById('edit_price');
            

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    
                    const id = this.getAttribute('data-id');
                    editForm.action = this.getAttribute('data-url');


                    
                    editName.value = this.getAttribute('data-name');
                    editPrice.value = this.getAttribute('data-price');
                });
            });
        });
    </script>   
@endsection