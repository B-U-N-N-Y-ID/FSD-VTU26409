package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
public class StudentController {

    @Autowired
    StudentRepository repo;

    // CREATE
    @PostMapping("/add")
    public Student addStudent(@RequestBody Student s) {
        return repo.save(s);
    }

    // READ ALL
    @GetMapping("/students")
    public List<Student> getAll() {
        return repo.findAll();
    }

    // READ BY ID
    @GetMapping("/student/{id}")
    public Student getById(@PathVariable int id) {
        return repo.findById(id).orElse(null);
    }

    // UPDATE
    @PutMapping("/update/{id}")
    public Student update(@PathVariable int id, @RequestBody Student s) {
        Student old = repo.findById(id).orElse(null);
        if (old != null) {
            old.setName(s.getName());
            old.setEmail(s.getEmail());
            return repo.save(old);
        }
        return null;
    }

    // DELETE
    @DeleteMapping("/delete/{id}")
    public String delete(@PathVariable int id) {
        repo.deleteById(id);
        return "Deleted Successfully";
    }
}