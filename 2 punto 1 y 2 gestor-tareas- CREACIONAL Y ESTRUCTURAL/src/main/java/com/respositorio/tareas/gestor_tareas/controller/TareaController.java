package com.respositorio.tareas.gestor_tareas.controller;

import org.springframework.web.bind.annotation.*;
import com.respositorio.tareas.gestor_tareas.model.Tarea;
import com.respositorio.tareas.gestor_tareas.service.TareaService;

import java.util.List;

@RestController
@RequestMapping("/tareas")
public class TareaController {
/* FACADE: Oculta la complejidad del acceso a datos y operaciones, actuando como fachada para los controladores. */
    private final TareaService tareaService;
    public TareaController(TareaService tareaService) {   
        this.tareaService = tareaService;
    }

    @GetMapping
    public List<Tarea> listarTareas() {
        return tareaService.listarTareas();
    }

    @PostMapping
    public Tarea agregarTarea(@RequestBody Tarea tarea) {
        return tareaService.agregarTarea(tarea);
    }

    @PutMapping("/{id}")
    public Tarea actualizarTarea(@PathVariable Long id, @RequestBody Tarea tarea) {
        return tareaService.actualizarTarea(id, tarea);
    }

    @DeleteMapping("/{id}")
    public void borrarTarea(@PathVariable Long id) {
        tareaService.borrarTarea(id);
    }
}
