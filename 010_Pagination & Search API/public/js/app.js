class ProductAPI {
    constructor() {
        this.currentPage = 1;
        this.totalPages = 1;
        this.currentFilters = {};
        this.isSearching = false;
        this.init();
    }

    init() {
        this.loadCategories();
        this.loadProducts();
        this.setupEventListeners();
    }

    setupEventListeners() {
        document.getElementById('applyFilters').addEventListener('click', () => {
            this.currentPage = 1;
            this.collectFilters();
            this.loadProducts();
        });

        document.getElementById('searchBtn').addEventListener('click', () => {
            const searchTerm = document.getElementById('searchInput').value.trim();
            if (searchTerm) {
                this.performSearch(searchTerm);
            }
        });

        document.getElementById('searchInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const searchTerm = document.getElementById('searchInput').value.trim();
                if (searchTerm) {
                    this.performSearch(searchTerm);
                }
            }
        });

        document.getElementById('prevPage').addEventListener('click', () => {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.isSearching ? this.performSearch(this.lastSearchTerm) : this.loadProducts();
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.isSearching ? this.performSearch(this.lastSearchTerm) : this.loadProducts();
            }
        });

        document.getElementById('limit').addEventListener('change', () => {
            this.currentPage = 1;
            this.loadProducts();
        });
    }

    collectFilters() {
        this.currentFilters = {
            category: document.getElementById('category').value,
            min_price: document.getElementById('minPrice').value || null,
            max_price: document.getElementById('maxPrice').value || null,
            sort_by: document.getElementById('sortBy').value,
            sort_order: document.getElementById('sortOrder').value,
            limit: document.getElementById('limit').value,
            page: this.currentPage
        };
    }

    async loadCategories() {
        try {
            const response = await fetch('/api/products/');
            const data = await response.json();
            
            const categorySelect = document.getElementById('category');
            categorySelect.innerHTML = '<option value="">All Categories</option>';
            
            data.filters.available_categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category;
                option.textContent = category;
                categorySelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    async loadProducts() {
        this.collectFilters();
        
        // Build query string
        const params = new URLSearchParams();
        params.append('page', this.currentPage);
        params.append('limit', this.currentFilters.limit);
        
        if (this.currentFilters.category) params.append('category', this.currentFilters.category);
        if (this.currentFilters.min_price) params.append('min_price', this.currentFilters.min_price);
        if (this.currentFilters.max_price) params.append('max_price', this.currentFilters.max_price);
        if (this.currentFilters.sort_by) params.append('sort_by', this.currentFilters.sort_by);
        if (this.currentFilters.sort_order) params.append('sort_order', this.currentFilters.sort_order);
        
        const container = document.getElementById('productsContainer');
        const pagination = document.getElementById('pagination');
        
        container.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading products...</div>';
        
        try {
            const response = await fetch(`/api/products/?${params.toString()}`);
            const data = await response.json();
            
            // Update pagination info
            this.currentPage = data.pagination.current_page;
            this.totalPages = data.pagination.total_pages;
            
            // Display products
            this.displayProducts(data.products);
            
            // Update pagination controls
            this.updatePagination(data.pagination);
            
            // Show API response
            document.getElementById('apiResponse').textContent = 
                JSON.stringify(data, null, 2);
            
            // Hide search results if showing
            document.getElementById('searchResults').classList.add('hidden');
            this.isSearching = false;
            
        } catch (error) {
            container.innerHTML = `<div class="error">Error loading products: ${error.message}</div>`;
            console.error('Error:', error);
        }
    }

    async performSearch(term) {
        this.lastSearchTerm = term;
        this.isSearching = true;
        
        const container = document.getElementById('productsContainer');
        const searchResults = document.getElementById('searchResults');
        const searchContent = document.getElementById('searchContent');
        
        container.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
        
        try {
            const params = new URLSearchParams();
            params.append('q', term);
            params.append('page', this.currentPage);
            params.append('limit', document.getElementById('limit').value);
            
            const response = await fetch(`/api/products/search.php?${params.toString()}`);
            const data = await response.json();
            
            // Update pagination info
            this.currentPage = data.pagination.current_page;
            this.totalPages = data.pagination.total_pages;
            
            // Display search results
            this.displayProducts(data.results);
            
            // Update pagination controls
            this.updatePagination(data.pagination);
            
            // Show search results section
            searchContent.innerHTML = `
                <p>Found ${data.pagination.total_results} results for "${term}"</p>
            `;
            searchResults.classList.remove('hidden');
            
            // Show API response
            document.getElementById('apiResponse').textContent = 
                JSON.stringify(data, null, 2);
            
        } catch (error) {
            container.innerHTML = `<div class="error">Error searching: ${error.message}</div>`;
            console.error('Search error:', error);
        }
    }

    displayProducts(products) {
        const container = document.getElementById('productsContainer');
        
        if (products.length === 0) {
            container.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-box-open"></i>
                    <h3>No products found</h3>
                    <p>Try adjusting your filters or search term</p>
                </div>
            `;
            return;
        }
        
        const productsHTML = products.map(product => `
            <div class="product-card">
                <span class="category">${product.category}</span>
                <h3>${product.name}</h3>
                <p class="description">${product.description}</p>
                <div class="price">$${parseFloat(product.price).toFixed(2)}</div>
                <div class="meta">
                    <span>ID: ${product.id}</span>
                    <span>Stock: ${product.stock}</span>
                </div>
            </div>
        `).join('');
        
        container.innerHTML = `<div class="product-grid">${productsHTML}</div>`;
    }

    updatePagination(pagination) {
        const paginationDiv = document.getElementById('pagination');
        const pageInfo = document.getElementById('pageInfo');
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        
        if (pagination.total_pages <= 1) {
            paginationDiv.classList.add('hidden');
            return;
        }
        
        paginationDiv.classList.remove('hidden');
        pageInfo.textContent = `Page ${pagination.current_page} of ${pagination.total_pages}`;
        
        prevBtn.disabled = !pagination.has_prev;
        nextBtn.disabled = !pagination.has_next;
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ProductAPI();
});