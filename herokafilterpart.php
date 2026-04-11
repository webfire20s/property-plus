<div class="filter-card mx-auto shadow-lg" style="max-width: 1000px; background: #fff; padding: 30px; border-radius: 20px; margin-top: -30px; position: relative; z-index: 1000; border: 1px solid #e2e8f0;">
            <div class="mb-3 ps-1">
                <span class="fw-bold" style="font-size: 0.9rem; color: #64748b;"><i class="fa-solid fa-sliders me-2"></i>Filter Property Search</span>
            </div>
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fa-solid fa-location-dot text-muted"></i></span>
                        <input name="city" class="form-control border-0 bg-light" placeholder="Search City..." value="<?= $_GET['city'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select border-0 bg-light">
                        <option value="">Category</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat ?>" <?= (($_GET['category'] ?? '')==$cat)?'selected':'' ?>>
                                <?= $cat ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="purpose" class="form-select border-0 bg-light">
                        <option value="">Purpose</option>
                        <option value="buy" <?= (($_GET['purpose'] ?? '')=='buy')?'selected':'' ?>>Buy</option>
                        <option value="sell" <?= (($_GET['purpose'] ?? '')=='sell')?'selected':'' ?>>Sell</option>
                        <option value="rent" <?= (($_GET['purpose'] ?? '')=='rent')?'selected':'' ?>>Rent</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="min_price" class="form-control border-0 bg-light" placeholder="Min ₹">
                </div>
                <div class="col-md-2">
                    <input type="number" name="max_price" class="form-control border-0 bg-light" placeholder="Max ₹">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-success w-100" style="background: #2eca6a; border: none; height: 100%;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>
        </div>