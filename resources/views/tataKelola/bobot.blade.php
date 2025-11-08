@extends('layout.app')

@section('title', 'Bobot')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
            <h1>Bobot</h1>    
        </div>
        <div class="section-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @elseif($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        
            <div class="card-body">
                <form id="editForm" action="{{ route('bobot.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="table-responsive">
                        <table class="table table-striped table-rounded-outer">
                            <thead>
                                <tr style="background-color: #d6c5b5;">
                                    <th>Elemen / Sub Elemen</th>
                                    <th class="text-center">Bobot</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($elements as $element)
                                    <tr style="background-color: white;"> 
                                        <td class="font-weight-bold">
                                            <span class="toggle-sub-elemen" onclick="toggleSubElements('{{ $element->id }}')" style="cursor: pointer;">
                                                {{ $element->elemen }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="bobot-text font-weight-bold">{{ $element->bobot_elemen }}</span>
                                            <input type="text" name="bobot_elemen[{{ $element->id }}]" class="form-control bobot-input font-weight-bold" value="{{ $element->bobot_elemen }}" style="display: none;">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-icon btn-primary btn-edit" onclick="editRow(this, '{{ $element->id }}')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button type="submit" class="btn btn-icon btn-success btn-save" style="display: none;">
                                                <i class="fas fa-save"></i> Simpan
                                            </button>
                                        </td>
                                    </tr>
                                    @foreach($element->ManagementSubElement as $subElement)
                                        <tr id="subElementRow{{ $subElement->id }}" class="sub-elemen sub-elemen-{{ $element->id }}" style="display: none; background-color: white;"> <!-- White background for sub-elements -->
                                            <td class="pl-4">{{ $subElement->sub_elemen }}</td>
                                            <td class="text-center">
                                                <span class="bobot-text font-weight-bold">{{ $subElement->bobot_sub_elemen }}</span>
                                                <input type="text" name="bobot_sub_elemen[{{ $subElement->id }}]" class="form-control bobot-input font-weight-bold" value="{{ $subElement->bobot_sub_elemen }}" style="display: none;">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-icon btn-primary btn-edit" onclick="editRow(this, '{{ $subElement->id }}', true)">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button type="submit" class="btn btn-icon btn-success btn-save" style="display: none;">
                                                    <i class="fas fa-save"></i> Simpan
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<script>
    setTimeout(function() {
        let alertElement = document.querySelector('.alert');
        if (alertElement) {
            alertElement.style.transition = 'opacity 1s';
            alertElement.style.opacity = '0';
            setTimeout(() => alertElement.remove(), 1000);
        }
    }, 2000);
    
    function editRow(button, id, isSubElement = false) {
        const row = button.closest('tr');
        row.querySelector('.bobot-text').style.display = 'none';
        row.querySelector('.bobot-input').style.display = 'block';

        button.style.display = 'none';
        row.querySelector('.btn-save').style.display = 'inline-block';
    }

    function toggleSubElements(elementId) {
        const subElements = document.querySelectorAll('.sub-elemen-' + elementId);
        const isExpanded = subElements[0].style.display !== 'none';

        subElements.forEach(function(subElement) {
            subElement.style.display = isExpanded ? 'none' : 'table-row';
            if (!isExpanded) {
                localStorage.setItem('openElement' + elementId, 'true');
            } else {
                localStorage.removeItem('openElement' + elementId);
            }
        });
    }

    function openPreviouslyOpenedElements() {
        const elements = document.querySelectorAll('[id^="elementRow"]');
        elements.forEach(function(element) {
            const elementId = element.id.replace('elementRow', '');
            const isOpen = localStorage.getItem('openElement' + elementId);
            if (isOpen) {
                const subElements = document.querySelectorAll('.sub-elemen-' + elementId);
                subElements.forEach(function(subElement) {
                    subElement.style.display = 'table-row';
                });
            }
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        openPreviouslyOpenedElements();
    });
</script>
@endsection
