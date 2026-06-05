<div class="card mb-4 shadow-sm border-0 rounded-4 overflow-hidden animate-in">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3 d-flex align-items-start text-dark">
            <span class="badge bg-primary me-3 px-3 py-2">Question</span>
            <?= e($question['texte_question']) ?>
        </h5>
        
        <?php if (!empty($question['image_path'])): ?>
            <div class="text-center my-4">
                <img src="../images/<?= e($question['image_path']) ?>" 
                     class="img-fluid rounded-4 shadow-sm border" 
                     style="max-height: 280px; width: auto; border: 5px solid #f8f9fa;">
            </div>
        <?php endif; ?>

        <div class="row g-3 mt-2">
            <?php foreach ($reponses as $r): ?>
                <div class="col-md-6">
                    <?php $uniqueId = "rep" . $r['id_reponse']; ?>
                    
                    <input type="<?= ($question['type_question'] === 'multiple') ? 'checkbox' : 'radio' ?>" 
                           name="reponse[<?= $question['id_question'] ?>][]" 
                           value="<?= $r['id_reponse'] ?>" 
                           class="btn-check" 
                           id="<?= $uniqueId ?>"
                           <?= ($question['type_question'] === 'unique') ? 'required' : '' ?>> <label class="btn btn-outline-primary w-100 h-100 p-3 shadow-sm d-flex flex-column align-items-center justify-content-center border-2 rounded-4 transition-all" 
                           for="<?= $uniqueId ?>" 
                           style="min-height: 100px; cursor: pointer;">
                        
                        <?php if (!empty($r['image_reponse'])): ?>
                            <div class="mb-2 w-100 text-center">
                                <img src="../images/<?= e($r['image_reponse']) ?>" 
                                     class="img-fluid rounded-3" 
                                     style="max-height: 140px; width: 100%; object-fit: cover;">
                            </div>
                        <?php endif; ?>

                        <div class="fw-bold text-wrap px-2"><?= e($r['texte_reponse']) ?></div>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>