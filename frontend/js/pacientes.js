let currentPage = 1;
let currentSearch = '';

document.addEventListener('DOMContentLoaded', function() {
    if (!Auth.isAuthenticated()) {
        window.location.href = 'login.html';
        return;
    }

    loadCatalogos();
    loadPacientes();
});

async function loadCatalogos() {
    try {
        const [tiposDoc, generos, departamentos] = await Promise.all([
            API.getTiposDocumento(),
            API.getGeneros(),
            API.getDepartamentos()
        ]);

        fillSelect('tipo_documento_id', tiposDoc.data);
        fillSelect('genero_id', generos.data);
        fillSelect('departamento_id', departamentos.data);
    } catch (error) {
        showMessage('Error al cargar catálogos: ' + error.message, 'danger');
    }
}

function fillSelect(selectId, data) {
    const select = document.getElementById(selectId);
    const currentValue = select.value;
    select.innerHTML = select.querySelector('option[value=""]') ? select.querySelector('option[value=""]').outerHTML : '<option value="">Seleccione...</option>';
    
    data.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = item.nombre;
        select.appendChild(option);
    });
    
    if (currentValue) {
        select.value = currentValue;
    }
}

async function loadMunicipios() {
    const departamentoId = document.getElementById('departamento_id').value;
    const municipioSelect = document.getElementById('municipio_id');
    
    if (!departamentoId) {
        municipioSelect.innerHTML = '<option value="">Seleccione departamento primero</option>';
        return;
    }

    try {
        const response = await API.getMunicipios(departamentoId);
        fillSelect('municipio_id', response.data);
    } catch (error) {
        showMessage('Error al cargar municipios: ' + error.message, 'danger');
    }
}

async function loadPacientes(page = 1) {
    const tbody = document.getElementById('pacientesTableBody');
    const paginationEl = document.getElementById('pagination');
    
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="spinner-border text-turquoise" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="text-white-50 mt-3">Cargando pacientes...</p>
                </td>
            </tr>
        `;
    
    try {
        const response = await API.getPacientes(page, 10, currentSearch);
        
        if (response.success) {
            displayPacientes(response.data.pacientes);
            displayPagination(response.data.pagination);
            updateTotalPacientes(response.data.pagination.total);
            currentPage = page;
            
            if (response.data.pacientes.length === 0 && currentSearch) {
                showMessage(`No se encontraron pacientes que coincidan con "${currentSearch}"`, 'info', 4000);
            }
        }
    } catch (error) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5">
                    <i class="bi bi-exclamation-triangle display-4 text-danger d-block mb-3"></i>
                    <p class="text-white">Error al cargar pacientes</p>
                    <button class="btn btn-primary mt-3" onclick="loadPacientes(${page})">
                        <i class="bi bi-arrow-clockwise"></i> Reintentar
                    </button>
                </td>
            </tr>
        `;
        showMessage(`Error al cargar pacientes: ${error.message}`, 'danger', 6000);
    }
}

function displayPacientes(pacientes) {
    const tbody = document.getElementById('pacientesTableBody');
    
    if (pacientes.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-turquoise-light d-block mb-3"></i>
                    <p class="text-white-50 fs-5">No hay pacientes registrados</p>
                    <button class="btn btn-primary mt-3 shadow-sm" onclick="showCreateForm()">
                        <i class="bi bi-person-plus-fill me-2"></i>Crear Primer Paciente
                    </button>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = pacientes.map(paciente => {
        let fotoHTML;
        const nombreParaFoto = `${paciente.nombre1} ${paciente.apellido1}`;
        if (paciente.foto) {
            const fotoEscaped = paciente.foto.replace(/'/g, "\\'").replace(/"/g, '&quot;');
            fotoHTML = `<img src="${paciente.foto}" alt="Foto de ${paciente.nombre1}" class="patient-photo shadow-sm" style="width: 45px; height: 45px; object-fit: cover; border-radius: 50%; border: 2px solid var(--color-turquoise); cursor: pointer; transition: transform 0.3s;" onclick="showPhotoModal('${fotoEscaped}', '${nombreParaFoto}')" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">`;
        } else {
            fotoHTML = `<div class="no-photo shadow-sm" style="width: 45px; height: 45px; border-radius: 50%; background: var(--color-turquoise-translucent-2); display: flex; align-items: center; justify-content: center; border: 2px solid var(--color-turquoise-translucent-3); margin: 0 auto;"><i class="bi bi-person-fill text-turquoise" style="font-size: 1.2rem;"></i></div>`;
        }
        
        const nombreCompleto = `${paciente.nombre1} ${paciente.nombre2 || ''} ${paciente.apellido1} ${paciente.apellido2 || ''}`.trim();
        const nombreCorto = nombreCompleto.length > 25 ? nombreCompleto.substring(0, 22) + '...' : nombreCompleto;
        const ubicacion = `${paciente.municipio_nombre}, ${paciente.departamento_nombre}`;
        
        return `
        <tr class="table-row-hover align-middle">
            <td class="text-center">${fotoHTML}</td>
            <td>
                <span class="badge bg-dark text-white">#${paciente.id}</span>
            </td>
            <td>
                <small class="text-muted d-block" style="font-size: 0.75rem;">${paciente.tipo_documento_nombre || 'N/A'}</small>
                <strong class="text-white" style="font-size: 0.85rem;">${paciente.numero_documento}</strong>
            </td>
            <td>
                <strong class="text-white" style="font-size: 0.85rem; word-break: break-word;">${nombreCompleto}</strong>
            </td>
            <td>
                <span class="text-white" style="font-size: 0.8rem; word-break: break-word;">${paciente.correo}</span>
            </td>
            <td>
                <span class="badge bg-info text-dark" style="font-size: 0.75rem;">${paciente.genero_nombre}</span>
            </td>
            <td>
                <small class="text-white" style="font-size: 0.8rem; word-break: break-word;">${ubicacion}</small>
            </td>
            <td class="text-center">
                <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-warning shadow-sm" onclick="editPaciente(${paciente.id})" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar paciente" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-danger shadow-sm" onclick="deletePaciente(${paciente.id}, this)" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar paciente" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
    }).join('');
    
    initializeTooltips();
}

function updateTotalPacientes(total) {
    const totalEl = document.getElementById('totalPacientes');
    if (totalEl) {
        totalEl.textContent = `${total} ${total === 1 ? 'paciente' : 'pacientes'}`;
    }
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('clearSearchBtn').style.display = 'none';
    currentSearch = '';
    loadPacientes(1);
}

function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function displayPagination(pagination) {
    const paginationEl = document.getElementById('pagination');
    paginationEl.innerHTML = '';

    if (pagination.total_pages <= 1) return;

    for (let i = 1; i <= pagination.total_pages; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === pagination.page ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#" onclick="loadPacientes(${i}); return false;">${i}</a>`;
        paginationEl.appendChild(li);
    }
}

let searchTimeout;
function debounceSearch() {
    const searchValue = document.getElementById('searchInput').value.trim();
    const clearBtn = document.getElementById('clearSearchBtn');
    
    if (clearBtn) {
        clearBtn.style.display = searchValue ? 'block' : 'none';
    }
    
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        searchPacientes();
    }, 500);
}

function searchPacientes() {
    currentSearch = document.getElementById('searchInput').value.trim();
    loadPacientes(1);
}

function showCreateForm() {
    document.getElementById('modalTitle').textContent = 'Nuevo Paciente';
    document.getElementById('pacienteForm').reset();
    document.getElementById('pacienteId').value = '';
    document.getElementById('municipio_id').innerHTML = '<option value="">Seleccione departamento primero</option>';
    document.getElementById('fotoPreview').innerHTML = '';
    document.getElementById('foto').value = '';
    
    const submitBtn = document.querySelector('#pacienteForm button[type="submit"]');
    submitBtn.innerHTML = '<i class="bi bi-person-plus me-2"></i>Crear Paciente';
    submitBtn.disabled = false;
    
    const modal = new bootstrap.Modal(document.getElementById('pacienteModal'));
    modal.show();
}

async function editPaciente(id) {
    try {
        const response = await API.getPaciente(id);
        
        if (response.success) {
            const paciente = response.data;
            
            document.getElementById('modalTitle').textContent = 'Editar Paciente';
            document.getElementById('pacienteId').value = paciente.id;
            document.getElementById('tipo_documento_id').value = paciente.tipo_documento_id;
            document.getElementById('numero_documento').value = paciente.numero_documento;
            document.getElementById('nombre1').value = paciente.nombre1;
            document.getElementById('nombre2').value = paciente.nombre2 || '';
            document.getElementById('apellido1').value = paciente.apellido1;
            document.getElementById('apellido2').value = paciente.apellido2 || '';
            document.getElementById('genero_id').value = paciente.genero_id;
            document.getElementById('correo').value = paciente.correo;
            document.getElementById('departamento_id').value = paciente.departamento_id;
            
            await loadMunicipios();
            setTimeout(() => {
                document.getElementById('municipio_id').value = paciente.municipio_id;
            }, 300);
            
            if (paciente.foto) {
                const fotoEscaped = paciente.foto.replace(/'/g, "\\'").replace(/"/g, '&quot;');
                document.getElementById('fotoPreview').innerHTML = `
                    <div class="mb-2">
                        <label class="form-label text-turquoise">Foto actual:</label>
                        <div class="text-center">
                            <img src="${paciente.foto}" alt="Foto actual" 
                                 class="img-thumbnail" style="max-width: 200px; max-height: 200px; border: 2px solid #40e0d0; border-radius: 10px; cursor: pointer;"
                                 onclick="showPhotoModal('${fotoEscaped}', '${paciente.nombre1} ${paciente.apellido1}')">
                            <p class="text-white-50 mt-2 small">
                                <i class="bi bi-info-circle"></i> Selecciona una nueva foto para reemplazarla
                            </p>
                        </div>
                    </div>
                `;
            } else {
                document.getElementById('fotoPreview').innerHTML = '';
            }
            
            const submitBtn = document.querySelector('#pacienteForm button[type="submit"]');
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Actualizar Paciente';
            submitBtn.disabled = false;
            
            const modal = new bootstrap.Modal(document.getElementById('pacienteModal'));
            modal.show();
        }
    } catch (error) {
        showMessage('Error al cargar paciente: ' + error.message, 'danger');
    }
}

async function deletePaciente(id, buttonElement) {
    try {
        const pacienteResponse = await API.getPaciente(id);
        if (!pacienteResponse.success) {
            showMessage('No se pudo cargar la información del paciente', 'danger');
            return;
        }
        
        const paciente = pacienteResponse.data;
        const nombreCompleto = `${paciente.nombre1} ${paciente.apellido1}`;
        const mensaje = `¿Está seguro de eliminar al paciente "${nombreCompleto}"?\n\nEsta acción no se puede deshacer.`;
        
        if (!confirm(mensaje)) {
            return;
        }

        const deleteBtn = buttonElement || document.querySelector(`button[onclick*="deletePaciente(${id}"]`);
        const originalText = deleteBtn.innerHTML;
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Eliminando...';

        const response = await API.deletePaciente(id);
        if (response.success) {
            showMessage(`<strong>Paciente eliminado correctamente</strong><br>El paciente ${nombreCompleto} ha sido eliminado del sistema.`, 'success', 4000);
            loadPacientes(currentPage);
        }
    } catch (error) {
        showMessage(`Error al eliminar paciente: ${error.message}`, 'danger', 6000);
        const deleteBtn = buttonElement || document.querySelector(`button[onclick*="deletePaciente(${id}"]`);
        if (deleteBtn) {
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = '<i class="bi bi-trash"></i> Eliminar';
        }
    }
}

function logout() {
    if (confirm('¿Desea cerrar sesión?')) {
        Auth.logout();
    }
}

function showPhotoModal(photoSrc, nombreCompleto) {
    const modalHTML = `
        <div class="modal fade" id="photoModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark border border-turquoise">
                    <div class="modal-header border-bottom border-turquoise">
                        <h5 class="modal-title text-white">
                            <i class="bi bi-image me-2"></i>Foto de ${nombreCompleto}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center p-4">
                        <img src="${photoSrc}" alt="Foto de ${nombreCompleto}" 
                             class="img-fluid rounded" style="max-height: 500px; border: 2px solid #40e0d0;">
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const existingModal = document.getElementById('photoModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const modal = new bootstrap.Modal(document.getElementById('photoModal'));
    modal.show();
    
    document.getElementById('photoModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

async function previewFoto(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('fotoPreview');
    const pacienteId = document.getElementById('pacienteId').value;
    
    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            showMessage('La imagen no puede exceder 5MB', 'warning', 5000);
            event.target.value = '';
            restoreCurrentPhoto(pacienteId);
            return;
        }
        if (!file.type.startsWith('image/')) {
            showMessage('El archivo debe ser una imagen', 'warning', 5000);
            event.target.value = '';
            restoreCurrentPhoto(pacienteId);
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const nombre1 = document.getElementById('nombre1').value || 'Nuevo';
            const apellido1 = document.getElementById('apellido1').value || 'paciente';
            const nombreCompleto = `${nombre1} ${apellido1}`;
            const fotoEscaped = e.target.result.replace(/'/g, "\\'").replace(/"/g, '&quot;');
            
            preview.innerHTML = `
                <div class="mb-2">
                    <label class="form-label text-turquoise">${pacienteId ? 'Nueva foto (reemplazará la actual):' : 'Vista previa:'}</label>
                    <div class="text-center">
                        <img src="${e.target.result}" alt="Vista previa" 
                             class="img-thumbnail" style="max-width: 200px; max-height: 200px; border: 2px solid #40e0d0; border-radius: 10px; cursor: pointer;"
                             onclick="showPhotoModal('${fotoEscaped}', '${nombreCompleto}')">
                        <p class="text-white-50 mt-2 small">
                            <i class="bi bi-check-circle text-success"></i> Foto seleccionada correctamente
                        </p>
                    </div>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    } else {
        await restoreCurrentPhoto(pacienteId);
    }
}

async function restoreCurrentPhoto(pacienteId) {
    const preview = document.getElementById('fotoPreview');
    
    if (pacienteId) {
        try {
            const pacienteResponse = await API.getPaciente(pacienteId);
            if (pacienteResponse.success && pacienteResponse.data.foto) {
                const paciente = pacienteResponse.data;
                const fotoEscaped = paciente.foto.replace(/'/g, "\\'").replace(/"/g, '&quot;');
                preview.innerHTML = `
                    <div class="mb-2">
                        <label class="form-label text-turquoise">Foto actual:</label>
                        <div class="text-center">
                            <img src="${paciente.foto}" alt="Foto actual" 
                                 class="img-thumbnail" style="max-width: 200px; max-height: 200px; border: 2px solid #40e0d0; border-radius: 10px; cursor: pointer;"
                                 onclick="showPhotoModal('${fotoEscaped}', '${paciente.nombre1} ${paciente.apellido1}')">
                            <p class="text-white-50 mt-2 small">
                                <i class="bi bi-info-circle"></i> Selecciona una nueva foto para reemplazarla
                            </p>
                        </div>
                    </div>
                `;
            } else {
                preview.innerHTML = '';
            }
        } catch (error) {
            preview.innerHTML = '';
        }
    } else {
        preview.innerHTML = '';
    }
}

function convertFileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
    });
}

document.getElementById('pacienteForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const validationErrors = validateForm();
    if (validationErrors.length > 0) {
        showMessage(validationErrors.join('<br>'), 'danger', 7000);
        return;
    }

    showLoading(true);

    const pacienteId = document.getElementById('pacienteId').value;
    const fotoInput = document.getElementById('foto');
    let fotoBase64 = null;
    
    if (fotoInput.files.length > 0) {
        const file = fotoInput.files[0];
        if (file.size > 5 * 1024 * 1024) {
            showLoading(false);
            showMessage('La imagen no puede exceder 5MB', 'warning', 5000);
            return;
        }
        if (!file.type.startsWith('image/')) {
            showLoading(false);
            showMessage('El archivo debe ser una imagen', 'warning', 5000);
            return;
        }
        fotoBase64 = await convertFileToBase64(file);
    } else if (pacienteId) {
        try {
            const pacienteResponse = await API.getPaciente(pacienteId);
            if (pacienteResponse.success && pacienteResponse.data.foto) {
                fotoBase64 = pacienteResponse.data.foto;
            }
        } catch (error) {
            console.log('No se pudo obtener la foto existente, se actualizará sin foto');
        }
    }
    
    const data = {
        tipo_documento_id: parseInt(document.getElementById('tipo_documento_id').value),
        numero_documento: document.getElementById('numero_documento').value.trim(),
        nombre1: document.getElementById('nombre1').value.trim(),
        nombre2: document.getElementById('nombre2').value.trim() || null,
        apellido1: document.getElementById('apellido1').value.trim(),
        apellido2: document.getElementById('apellido2').value.trim() || null,
        genero_id: parseInt(document.getElementById('genero_id').value),
        departamento_id: parseInt(document.getElementById('departamento_id').value),
        municipio_id: parseInt(document.getElementById('municipio_id').value),
        correo: document.getElementById('correo').value.trim(),
        foto: fotoBase64
    };

    try {
        let response;
        if (pacienteId) {
            response = await API.updatePaciente(pacienteId, data);
            if (response.success) {
                showMessage(`<strong>Paciente actualizado correctamente</strong><br>Los cambios se han guardado exitosamente.`, 'success', 4000);
            }
        } else {
            response = await API.createPaciente(data);
            if (response.success) {
                showMessage(`<strong>Paciente creado correctamente</strong><br>El paciente ha sido registrado en el sistema.`, 'success', 4000);
            }
        }

        if (response.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('pacienteModal'));
            modal.hide();
            setTimeout(() => {
                loadPacientes(currentPage);
            }, 500);
        }
    } catch (error) {
        const errorMessage = error.message.includes('duplicado') || error.message.includes('ya existe') 
            ? 'El número de documento ya está registrado en el sistema'
            : error.message.includes('correo') && error.message.includes('duplicado')
            ? 'El correo electrónico ya está registrado'
            : `Error al ${pacienteId ? 'actualizar' : 'crear'} el paciente: ${error.message}`;
        showMessage(errorMessage, 'danger', 6000);
    } finally {
        showLoading(false);
    }
});

function showMessage(message, type, duration = 5000) {
    const container = document.getElementById('messageContainer');
    const icon = type === 'success' ? '<i class="bi bi-check-circle-fill me-2"></i>' : 
                 type === 'danger' ? '<i class="bi bi-exclamation-triangle-fill me-2"></i>' :
                 type === 'warning' ? '<i class="bi bi-exclamation-circle-fill me-2"></i>' :
                 '<i class="bi bi-info-circle-fill me-2"></i>';
    
    container.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${icon}${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => {
                container.innerHTML = '';
            }, 300);
        }
    }, duration);
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validateDocumento(numero) {
    const documentoRegex = /^[0-9]{7,15}$/;
    return documentoRegex.test(numero.trim());
}

function validateForm() {
    const errors = [];
    
    const tipoDocumento = document.getElementById('tipo_documento_id').value;
    const numeroDocumento = document.getElementById('numero_documento').value.trim();
    const nombre1 = document.getElementById('nombre1').value.trim();
    const apellido1 = document.getElementById('apellido1').value.trim();
    const genero = document.getElementById('genero_id').value;
    const correo = document.getElementById('correo').value.trim();
    const departamento = document.getElementById('departamento_id').value;
    const municipio = document.getElementById('municipio_id').value;
    
    if (!tipoDocumento) {
        errors.push('Debe seleccionar un tipo de documento');
    }
    
    if (!numeroDocumento) {
        errors.push('El número de documento es obligatorio');
    } else if (!validateDocumento(numeroDocumento)) {
        errors.push('El número de documento debe contener entre 7 y 15 dígitos');
    }
    
    if (!nombre1) {
        errors.push('El primer nombre es obligatorio');
    } else if (nombre1.length < 2) {
        errors.push('El primer nombre debe tener al menos 2 caracteres');
    } else if (nombre1.length > 50) {
        errors.push('El primer nombre no puede exceder 50 caracteres');
    }
    
    if (!apellido1) {
        errors.push('El primer apellido es obligatorio');
    } else if (apellido1.length < 2) {
        errors.push('El primer apellido debe tener al menos 2 caracteres');
    } else if (apellido1.length > 50) {
        errors.push('El primer apellido no puede exceder 50 caracteres');
    }
    
    if (!genero) {
        errors.push('Debe seleccionar un género');
    }
    
    if (!correo) {
        errors.push('El correo electrónico es obligatorio');
    } else if (!validateEmail(correo)) {
        errors.push('El formato del correo electrónico no es válido');
    }
    
    if (!departamento) {
        errors.push('Debe seleccionar un departamento');
    }
    
    if (!municipio) {
        errors.push('Debe seleccionar un municipio');
    }
    
    const nombre2 = document.getElementById('nombre2').value.trim();
    if (nombre2 && nombre2.length > 50) {
        errors.push('El segundo nombre no puede exceder 50 caracteres');
    }
    
    const apellido2 = document.getElementById('apellido2').value.trim();
    if (apellido2 && apellido2.length > 50) {
        errors.push('El segundo apellido no puede exceder 50 caracteres');
    }
    
    return errors;
}

function showLoading(show = true) {
    const submitBtn = document.querySelector('#pacienteForm button[type="submit"]');
    if (show) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
    } else {
        submitBtn.disabled = false;
        const pacienteId = document.getElementById('pacienteId').value;
        submitBtn.innerHTML = pacienteId ? '<i class="bi bi-check-circle me-2"></i>Actualizar Paciente' : '<i class="bi bi-person-plus me-2"></i>Crear Paciente';
    }
}
