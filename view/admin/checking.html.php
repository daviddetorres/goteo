<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

$filters = $this['filters'];

//arrastramos los filtros
$filter = "?status={$filters['status']}&checker={$filters['checker']}";

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Revisión de proyectos</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/overview">Listado de proyectos</a></li>
                        <li><a href="/admin/managing">Revisores</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php if (!empty($this['errors']) || !empty($this['message'])) : ?>
                <div class="widget">
                    <p>
                        <?php echo implode(',', $this['errors']); ?>
                        <?php echo $this['message']; ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="widget board">
                <form id="filter-form" action="/admin/checking" method="get">
                    <label for="status-filter">Mostrar por estado:</label>
                    <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Todas</option>
                    <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
                        <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
                    <?php endforeach; ?>
                    </select>

                    <label for="checker-filter">Asignados a:</label>
                    <select id="checker-filter" name="checker" onchange="document.getElementById('filter-form').submit();">
                        <option value="">De todos</option>
                    <?php foreach ($this['checkers'] as $checker) : ?>
                        <option value="<?php echo $checker->id; ?>"<?php if ($filters['checker'] == $checker->id) echo ' selected="selected"';?>><?php echo $checker->name; ?></option>
                    <?php endforeach; ?>
                    </select>
                </form>
            </div>

                <?php if (!empty($this['projects'])) : ?>
                    <?php foreach ($this['projects'] as $project) : ?>
                        <div class="widget board">
                            <table>
                                <thead>
                                    <tr>
                                        <th width="30%">Proyecto</th> <!-- edit -->
                                        <th width="20%">Creador</th> <!-- mailto -->
                                        <th width="5%">%</th> <!-- segun estado -->
                                        <th width="5%">Puntos</th> <!-- segun estado -->
                                        <th>
                                            <!-- Iniciar revision si no tiene registro de revision -->
                                            <!-- Editar si tiene registro -->
                                        </th>
                                        <th><!-- Ver informe si tiene registro --></th>
                                        <th><!-- Cerar si abierta --></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td><a href="/project/<?php echo $project->project; ?>" target="_blank" title="Preview"><?php echo $project->name; ?></a></td>
                                        <td><?php echo $project->owner; ?></td>
                                        <td><?php echo $project->progress; ?></td>
                                        <td><?php echo $project->score . ' / ' . $project->max; ?></td>
                                        <?php if (!empty($project->review)) : ?>
                                        <td><a href="/admin/checking/edit/<?php echo $project->project; ?>/<?php echo $filter; ?>">[Editar]</a></td>
                                        <td><a href="/admin/checking/report/<?php echo $project->project; ?>" target="_blank">[Ver informe]</a></td>
                                            <?php if ( $project->status > 0 ) : ?>
                                        <td><a href="/admin/checking/close/<?php echo $project->review; ?>/<?php echo $filter; ?>">[Cerrar]</a></td>
                                            <?php else : ?>
                                        <td>Revisión cerrada</td>
                                            <?php endif; ?>
                                        <?php else : ?>
                                        <td><a href="/admin/checking/add/<?php echo $project->project; ?>/<?php echo $filter; ?>">[Iniciar revision]</a></td>
                                        <td></td>
                                        <td></td>
                                        <?php endif; ?>
                                    </tr>
                                </tbody>

                            </table>

                            <?php if (!empty($project->review)) : ?>
                            <table>
                                <tr>
                                    <th>Revisor</th>
                                    <th>Puntos</th>
                                    <th>Listo</th>
                                    <th></th>
                                </tr>
                                <?php foreach ($project->checkers as $user=>$checker) : ?>
                                <tr>
                                    <td><?php echo $checker->name; ?></td>
                                    <td><?php echo $checker->score . '/' . $checker->max; ?></td>
                                    <td><?php if ($checker->ready) : ?>Listo <a href="/admin/checking/unready/<?php echo $project->review; ?>/<?php echo $filter; ?>&user=<?php echo $user; ?>">[Reabrir]</a><?php endif ?></td>
                                    <td><a href="/admin/checking/unassign/<?php echo $project->review; ?>/<?php echo $filter; ?>&user=<?php echo $user; ?>">[Desasignar]</a></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if ($project->status > 0) : ?>
                                <tr>
                                    <form id="form-assign-<?php echo $project->review; ?>" action="/admin/checking/assign/<?php echo $project->review; ?>/<?php echo $filter; ?>" method="get">
                                    <td colspan="2">
                                        <select name="user">
                                            <option value="">Selecciona un nuevo revisor</option>
                                            <?php foreach ($this['checkers'] as $user) :
                                                if (in_array($user->id, array_keys($project->checkers))) continue;
                                                ?>
                                            <option value="<?php echo $user->id; ?>"><?php echo $user->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><a href="#" onclick="document.getElementById('form-assign-<?php echo $project->review; ?>').submit(); return false;">[Asignar]</a></td>
                                    </form>
                                </tr>
                                <?php endif; ?>
                            </table>
                            <?php endif; ?>
                            
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                <p>No se han encontrado registros</p>
                <?php endif; ?>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';