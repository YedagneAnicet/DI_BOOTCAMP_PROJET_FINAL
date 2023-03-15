import { UtilisateurService } from './../../services/utilisateur.service';
import { Component, OnInit } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss'],
})
export class RegisterComponent implements OnInit {
  FormUtilisateur!: FormGroup;
  submitted = false

  constructor(
    private _fb: FormBuilder,
    private _utilisateur: UtilisateurService
  ) {}

  ngOnInit(): void {
    this.FormUtilisateur = this._fb.group({
      nom: [null, [Validators.required, Validators.minLength(3)]],
      prenoms: [null, [Validators.required, Validators.minLength(3)]],
      adresse: [null, [Validators.required, Validators.minLength(3)]],
      ville: [null, [Validators.required, Validators.minLength(3)]],
      telephone: [null, [Validators.required, Validators.minLength(3)]],
      email: [null, [Validators.required, Validators.email]],
      mdp: [null, [Validators.required, Validators.minLength(8)]],
    });
  }

  get f(): { [key: string]: AbstractControl } {
    return this.FormUtilisateur.controls;
  }

  onSubmit() {
    this.submitted = true
    const utilisateur = this.FormUtilisateur.value;
    this._utilisateur.inscription(utilisateur).subscribe(
      (response) => {
        console.log(response);
      },
      (error) => {
        console.log(error);
      }
    );
  }
}
