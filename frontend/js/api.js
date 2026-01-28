const API = {
    baseURL: 'http://localhost:8000/api',

    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
            }
        };

        const token = Auth.getToken();
        if (token) {
            defaultOptions.headers['Authorization'] = `Bearer ${token}`;
        }

        const config = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...(options.headers || {})
            }
        };

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error en la petición');
            }

            return data;
        } catch (error) {
            if (error.message.includes('401') || error.message.includes('No autorizado')) {
                Auth.logout();
                window.location.href = 'login.html';
            }
            throw error;
        }
    },

    async login(username, password) {
        return await this.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ username, password })
        });
    },

    async getPacientes(page = 1, limit = 10, search = '') {
        const params = new URLSearchParams({
            page,
            limit,
            ...(search && { search })
        });
        return await this.request(`/pacientes?${params}`);
    },

    async getPaciente(id) {
        return await this.request(`/pacientes/${id}`);
    },

    async createPaciente(data) {
        return await this.request('/pacientes', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    async updatePaciente(id, data) {
        return await this.request(`/pacientes/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    },

    async deletePaciente(id) {
        return await this.request(`/pacientes/${id}`, {
            method: 'DELETE'
        });
    },

    async getDepartamentos() {
        return await this.request('/catalogos/departamentos');
    },

    async getMunicipios(departamentoId) {
        return await this.request(`/catalogos/municipios?departamento_id=${departamentoId}`);
    },

    async getTiposDocumento() {
        return await this.request('/catalogos/tipos-documento');
    },

    async getGeneros() {
        return await this.request('/catalogos/generos');
    }
};
