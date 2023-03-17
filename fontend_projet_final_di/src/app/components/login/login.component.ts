import { UtilisateurService } from './../../services/utilisateur.service';
import { Component, OnInit } from '@angular/core';
import {
  AbstractControl,
  FormBuilder,
  FormGroup,
  Validators,
} from '@angular/forms';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
})
export class LoginComponent implements OnInit {
  loginForm!: FormGroup;
  error = '';
  submitted = false;
  message!: string;

  constructor(
    private _utilisateur: UtilisateurService,
    private _fb: FormBuilder
  ) {}

  ngOnInit() {
    this.loginForm = this._fb.group({
      email: [null, [Validators.required, Validators.email]],
      mdp: [null, [Validators.required, Validators.minLength(8)]],
    });
  }

  get f(): { [key: string]: AbstractControl } {
    return this.loginForm.controls;
  }

  onSubmit() {
    this.submitted = true;
    if (this.loginForm.invalid) {
      return;
    }

    const email = this.loginForm.controls['email'].value;
    const mdp = this.loginForm.controls['mdp'].value;

    this._utilisateur.connexion({ email, mdp }).subscribe(
      (data) => {
        console.log(data);
      },
      (error) => {
        this.error = "Nom d'utilisateur ou mot de passe incorrect";
      }
    );
  }
}
