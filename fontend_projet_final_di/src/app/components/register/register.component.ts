import { UtilisateurService } from './../../services/utilisateur.service';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss'],
})
export class RegisterComponent implements OnInit {
  FormUtilisateur!: FormGroup;
  message !: string;

  constructor(private _fb: FormBuilder, private _utilisateur : UtilisateurService) {}

  ngOnInit(): void {
    this.FormUtilisateur = this._fb.group({
      nom: [null],
      prenoms: [null],
      adresse: [null],
      ville: [null],
      telephone: [null],
      email: [null],
      mdp: [null],
    });
  }



  onSubmit() {
    const utilisateur = this.FormUtilisateur.value;
    this._utilisateur.inscription(utilisateur).subscribe(
      (response) => {
        console.log(response);
        this.message = 'Votre compte a été créé avec succès.';
        console.log(this.message )
      },
      (error) => {
        console.log(error);
        this.message = 'Une erreur est survenue lors de la création de votre compte.';
        console.log(this.message )
      }
    );
  }
}
