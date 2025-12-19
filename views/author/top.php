@ -1,82 +0,0 @@
<?php
use yii\web\View;

/* @var $this View */
/* @var $allYearsData array */
/* @var $yearsRange array */
/* @var $year int */

$this->title = 'Топ авторов';
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$currentYear = date('Y');
?>

<div class="author-top">
    <h1 id="dynamic-title">Лучшие авторы за <?= $currentYear ?> год</h1>

    <div class="year-selector" style="margin: 20px 0;">
        <div class="form-inline">
            <div class="form-group">
                <label for="year">Выберите год:</label>
                <select name="year" id="year" class="form-control"
                        onchange="showAuthors(this.value)"
                        style="margin: 0 10px; width: 120px;">
                    <?php for ($i = date('Y'); $i >= 1900; $i--): ?>
                        <option value="<?= $i ?>" <?= $currentYear == $i ? 'selected' : '' ?>>
                            <?= $i ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
    </div>

    <div id="authors-container">
        <?php if (isset($allYearsData[$currentYear])): ?>
            <?= $this->render('_top_authors_table', [
                'authors' => $allYearsData[$currentYear],
                'year' => $currentYear,
            ]) ?>
        <?php else: ?>
            <div class="alert alert-info">
                <p>За <strong><?= $currentYear ?></strong> год не найдено книг в каталоге </p>
                <p>Выберите другой год для просмотра отчета</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
  var authorsData = <?= json_encode($allYearsData) ?>;

  function showAuthors(year) {
    document.getElementById('dynamic-title').innerText = 'Лучшие авторы за ' + year + ' год';
    var authors = authorsData[year];
    var container = document.getElementById('authors-container');

    if (authors && authors.length > 0) {
      var html = '<div class="table-responsive">' +
          '<table class="table table-hover">' +
          '<thead><tr>' +
          '<th>№</th>' +
          '<th>Автор</th>' +
          '</tr></thead>' +
          '<tbody>';

      for (var i = 0; i < authors.length; i++) {
        html += '<tr>' +
            '<td>' + (i + 1) + '</td>' +
            '<td><strong>' + authors[i].fullName + '</strong></td>' +
            '</tr>';
      }
      html += '</tbody></table></div>';
      container.innerHTML = html;
    } else {
      container.innerHTML = '<div class="alert alert-info">' +
          '<p>За <strong>' + year + '</strong> год не найдено книг в каталоге </p>' +
          '<p>Выберите другой год для просмотра отчета</p>' +
          '</div>';
    }
  }
</script>